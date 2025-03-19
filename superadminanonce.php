<?php
session_start();
require_once 'bdd.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Définir si l'utilisateur est admin ou superadmin
$isSuperAdmin = isset($_SESSION['is_superadmin']) && $_SESSION['is_superadmin'] == 1;
$currentUser = $_SESSION['user_id'];

// Initialiser les variables
$message = '';
$error = '';
$messages = [];
$users = [];

// Récupérer la liste des utilisateurs pour les administrateurs
if ($isSuperAdmin) {
    try {
        $stmt = $connexion->prepare("SELECT id, pseudo FROM user");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Erreur lors de la récupération des utilisateurs: " . $e->getMessage();
    }
}

// Récupérer tous les messages
// Les administrateurs voient tous les messages, les utilisateurs normaux ne voient que leurs messages
try {
    if ($isSuperAdmin) {
        $stmt = $connexion->prepare(
            "SELECT m.*, u.pseudo as user_name 
             FROM message m 
             JOIN user u ON m.user_id = u.id 
             ORDER BY m.creea DESC"
        );
    } else {
        $stmt = $connexion->prepare(
            "SELECT m.*, u.pseudo as user_name 
             FROM message m 
             JOIN user u ON m.user_id = u.id 
             WHERE m.user_id = :user_id 
             ORDER BY m.creea DESC"
        );
        $stmt->bindParam(':user_id', $currentUser);
    }
    
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des messages: " . $e->getMessage();
}

// Fonction pour formater les tags sous forme de badges HTML
function formatTags($tagsJson) {
    if (empty($tagsJson)) {
        return '<span class="badge badge-secondary">Aucun tag</span>';
    }
    
    $tags = json_decode($tagsJson, true);
    if (!is_array($tags)) {
        return '<span class="badge badge-secondary">Aucun tag</span>';
    }
    
    $badgeColors = [
        'Pescetarien' => 'success',
        'Vegan' => 'success',
        'Végétarien' => 'info',
        'Desserts' => 'primary',
        'Sans Gluten' => 'danger',
        'Avec viande' => 'dark',
    ];
    
    $badgesHtml = '';
    foreach ($tags as $tag) {
        $color = isset($badgeColors[$tag]) ? $badgeColors[$tag] : 'secondary';
        $badgesHtml .= '<span class="badge badge-' . $color . ' mr-1">' . htmlspecialchars($tag) . '</span>';
    }
    
    return $badgesHtml;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $titre = trim($_POST['titre']);
    $content = trim($_POST['content']);
    $ingredients = trim($_POST['ingredients']);
    $quantite = trim($_POST['quantite']);
    $nomAdresse = trim($_POST['nom_adresse']);
    $lieu = trim($_POST['lieu']);
    $datePeremption = !empty($_POST['date_peremption']) ? $_POST['date_peremption'] : null;
    $isClaim = isset($_POST['is_claim']) ? 1 : 0;
    $userId = ($isSuperAdmin) && isset($_POST['user_id']) ? $_POST['user_id'] : $currentUser;
    
    $tags = isset($_POST['tags']) ? $_POST['tags'] : [];
    $tagsJson = json_encode($tags);
    
    $imagePath = null;
    if (!empty($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxFileSize = 5 * 1024 * 1024; // 5 MB
        
        if (!in_array($_FILES['image']['type'], $allowedTypes)) {
            $error = "Type de fichier non autorisé. Seuls les formats JPEG, PNG et GIF sont acceptés.";
        } elseif ($_FILES['image']['size'] > $maxFileSize) {
            $error = "Fichier trop volumineux. Taille maximale: 5 MB.";
        } else {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $targetFilePath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                $imagePath = $targetFilePath;
            } else {
                $error = "Erreur lors de l'upload du fichier.";
            }
        }
    }
    
    // Validation
    if (empty($titre) || empty($content)) {
        $error = "Le titre et le contenu sont obligatoires";
    } else {
        try {
            $stmt = $connexion->prepare(
                "INSERT INTO message (titre, content, ingredients, quantite, nom_adresse, lieu, 
                date_peremption, is_claim, user_id, tags, image_path) 
                VALUES (:titre, :content, :ingredients, :quantite, :nom_adresse, :lieu, 
                :date_peremption, :is_claim, :user_id, :tags, :image_path)"
            );
            
            $stmt->bindParam(':titre', $titre);
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':ingredients', $ingredients);
            $stmt->bindParam(':quantite', $quantite);
            $stmt->bindParam(':nom_adresse', $nomAdresse);
            $stmt->bindParam(':lieu', $lieu);
            $stmt->bindParam(':date_peremption', $datePeremption);
            $stmt->bindParam(':is_claim', $isClaim);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':tags', $tagsJson);
            $stmt->bindParam(':image_path', $imagePath);
            
            $stmt->execute();
            $message = "Annonce créée avec succès!";
            
            // Rediriger pour éviter la soumission multiple du formulaire
            header('Location: ' . $_SERVER['PHP_SELF'] . '?success=1');
            exit();
            
        } catch (PDOException $e) {
            $error = "Erreur lors de la création de l'annonce: " . $e->getMessage();
        }
    }
}

// Traitement de la modification d'un message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $messageId = $_POST['message_id'];
    $titre = trim($_POST['titre']);
    $content = trim($_POST['content']);
    $ingredients = trim($_POST['ingredients']);
    $quantite = trim($_POST['quantite']);
    $nomAdresse = trim($_POST['nom_adresse']);
    $lieu = trim($_POST['lieu']);
    $datePeremption = !empty($_POST['date_peremption']) ? $_POST['date_peremption'] : null;
    $isClaim = isset($_POST['is_claim']) ? 1 : 0;
    $userId = ($isSuperAdmin) && isset($_POST['user_id']) ? $_POST['user_id'] : $currentUser;
    
    // Gérer les tags
    $tags = isset($_POST['tags']) ? $_POST['tags'] : [];
    $tagsJson = json_encode($tags);
    
    // Vérifier si l'utilisateur a le droit de modifier ce message
    if (!$isSuperAdmin) {
        $checkOwner = $connexion->prepare("SELECT user_id FROM message WHERE id = :id");
        $checkOwner->bindParam(':id', $messageId);
        $checkOwner->execute();
        $owner = $checkOwner->fetch(PDO::FETCH_ASSOC);
        
        if ($owner['user_id'] != $currentUser) {
            $error = "Vous n'avez pas l'autorisation de modifier ce message.";
            header('Location: ' . $_SERVER['PHP_SELF'] . '?error=' . urlencode($error));
            exit();
        }
    }
    
    // Gérer l'upload d'image
    $imagePath = null;
    $updateImage = false;
    
    if (!empty($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxFileSize = 5 * 1024 * 1024; // 5 MB
        
        if (!in_array($_FILES['image']['type'], $allowedTypes)) {
            $error = "Type de fichier non autorisé. Seuls les formats JPEG, PNG et GIF sont acceptés.";
        } elseif ($_FILES['image']['size'] > $maxFileSize) {
            $error = "Fichier trop volumineux. Taille maximale: 5 MB.";
        } else {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $targetFilePath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                $imagePath = $targetFilePath;
                $updateImage = true;
                
                // Supprimer l'ancienne image si elle existe
                $getOldImage = $connexion->prepare("SELECT image_path FROM message WHERE id = :id");
                $getOldImage->bindParam(':id', $messageId);
                $getOldImage->execute();
                $oldImage = $getOldImage->fetch(PDO::FETCH_ASSOC);
                
                if (!empty($oldImage['image_path']) && file_exists($oldImage['image_path'])) {
                    unlink($oldImage['image_path']);
                }
            } else {
                $error = "Erreur lors de l'upload du fichier.";
            }
        }
    }
    
    // Validation
    if (empty($titre) || empty($content)) {
        $error = "Le titre et le contenu sont obligatoires";
    } else {
        try {
            // Si une nouvelle image est téléchargée, la mettre à jour; sinon, laisser l'image existante
            if ($updateImage) {
                $stmt = $connexion->prepare(
                    "UPDATE message SET 
                    titre = :titre, 
                    content = :content, 
                    ingredients = :ingredients, 
                    quantite = :quantite, 
                    nom_adresse = :nom_adresse, 
                    lieu = :lieu, 
                    date_peremption = :date_peremption, 
                    is_claim = :is_claim, 
                    user_id = :user_id, 
                    tags = :tags,
                    image_path = :image_path
                    WHERE id = :id"
                );
                $stmt->bindParam(':image_path', $imagePath);
            } else {
                $stmt = $connexion->prepare(
                    "UPDATE message SET 
                    titre = :titre, 
                    content = :content, 
                    ingredients = :ingredients, 
                    quantite = :quantite, 
                    nom_adresse = :nom_adresse, 
                    lieu = :lieu, 
                    date_peremption = :date_peremption, 
                    is_claim = :is_claim, 
                    user_id = :user_id, 
                    tags = :tags
                    WHERE id = :id"
                );
            }
            
            $stmt->bindParam(':titre', $titre);
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':ingredients', $ingredients);
            $stmt->bindParam(':quantite', $quantite);
            $stmt->bindParam(':nom_adresse', $nomAdresse);
            $stmt->bindParam(':lieu', $lieu);
            $stmt->bindParam(':date_peremption', $datePeremption);
            $stmt->bindParam(':is_claim', $isClaim);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':tags', $tagsJson);
            $stmt->bindParam(':id', $messageId);
            
            $stmt->execute();
            $message = "Annonce mise à jour avec succès!";
            
            // Rediriger pour éviter la soumission multiple du formulaire
            header('Location: ' . $_SERVER['PHP_SELF'] . '?success=1');
            exit();
            
        } catch (PDOException $e) {
            $error = "Erreur lors de la modification de l'annonce: " . $e->getMessage();
        }
    }
}

// Traitement de la suppression d'un message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $messageId = $_POST['message_id'];
    
    // Vérifier si l'utilisateur a le droit de supprimer ce message
    if (!$isSuperAdmin) {
        $checkOwner = $connexion->prepare("SELECT user_id FROM message WHERE id = :id");
        $checkOwner->bindParam(':id', $messageId);
        $checkOwner->execute();
        $owner = $checkOwner->fetch(PDO::FETCH_ASSOC);
        
        if ($owner['user_id'] != $currentUser) {
            $error = "Vous n'avez pas l'autorisation de supprimer cette annonce.";
            header('Location: ' . $_SERVER['PHP_SELF'] . '?error=' . urlencode($error));
            exit();
        }
    }
    
    try {
        // Supprimer l'image associée si elle existe
        $getImagePath = $connexion->prepare("SELECT image_path FROM message WHERE id = :id");
        $getImagePath->bindParam(':id', $messageId);
        $getImagePath->execute();
        $imagePath = $getImagePath->fetch(PDO::FETCH_ASSOC);
        
        if (!empty($imagePath['image_path']) && file_exists($imagePath['image_path'])) {
            unlink($imagePath['image_path']);
        }
        
        // Supprimer le message
        $stmt = $connexion->prepare("DELETE FROM message WHERE id = :id");
        $stmt->bindParam(':id', $messageId);
        $stmt->execute();
        
        $message = "Annonce supprimée avec succès!";
        
        // Rediriger pour éviter la soumission multiple
        header('Location: ' . $_SERVER['PHP_SELF'] . '?success=1');
        exit();
        
    } catch (PDOException $e) {
        $error = "Erreur lors de la suppression de l'annonce: " . $e->getMessage();
    }
}

// Message de succès en provenance des redirections
if (isset($_GET['success'])) {
    $message = "Opération réalisée avec succès!";
}

// Message d'erreur en provenance des redirections
if (isset($_GET['error'])) {
    $error = $_GET['error'];
}

// Liste des tags disponibles
$availableTags = [
    'Pescetarien', 'Vegan', 'Végétarien', 'Desserts', 'Sans Gluten', 'Avec viande'
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clichy | Gestion des Annonces</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all .min.css">
    <style>
        .message-card {
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .badge {
            margin-right: 5px;
            font-weight: normal;
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        .claim-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
        }
        .tag-container {
            margin-bottom: 15px;
        }
        .back-to-site {
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .btn-action {
            margin-right: 5px;
        }
        .form-card {
            background: #f9f9f9;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
        }
        #preview-image {
            max-width: 100%;
            max-height: 200px;
            display: none;
            margin-top: 10px;
        }
        .image-container {
            position: relative;
        }
        .image-container .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.6);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .image-container:hover .overlay {
            opacity: 1;
        }
        .filters {
            margin-bottom: 20px;
            padding: 15px;
            background: #f4f4f4;
            border-radius: 5px;
        }
        .page-title {
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .tag-badge {
            cursor: pointer;
        }
        .tag-badge.active {
            box-shadow: 0 0 0 2px #fff, 0 0 0 4px #007bff;
        }
        .edit-preview-image {
            max-width: 100%;
            max-height: 150px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="page-title">Gestion des Annonces</h1>
        
        <div class="back-to-site">
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Retour au site
            </a>
            
            <?php if (  $isSuperAdmin): ?>
                <a href="superadmin.php" class="btn btn-outline-primary ml-2">
                    <i class="fas fa-users-cog"></i> Gestion des utilisateurs
                </a>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> <?php echo $message; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        
        
        <!-- Filtres et recherche -->
        <div class="filters">
            <h4><i class="fas fa-filter"></i> Filtres</h4>
            <div class="row">
                <div class="col-md-4 mb-2">
                    <input type="text" id="searchInput" class="form-control" placeholder="Rechercher...">
                </div>
                
                <div class="col-md-4 mb-2">
                    <select id="filterType" class="form-control">
                        <option value="all">Tous les types</option>
                        <option value="claim">Déjà reclamé</option>
                        <option value="share">Non réclamé</option>
                    </select>
                </div>
                
                <div class="col-md-4 mb-2">
                    <button id="resetFilters" class="btn btn-outline-secondary btn-block">
                        <i class="fas fa-sync-alt"></i> Réinitialiser les filtres
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Liste des annonces -->
        <h3 class="mt-4 mb-3">
            <?php if ($isSuperAdmin): ?>
                <i class="fas fa-list"></i> Toutes les annonces
            <?php else: ?>
                <i class="fas fa-list"></i> Mes annonces
            <?php endif; ?>
        </h3>
        
        <div id="message-container" class="row">
            <?php if (empty($messages)): ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Aucune annonce disponible.
                    </div>
                </div>
            <?php endif; ?>
            
            <?php foreach ($messages as $msg): ?>
                <div class="col-lg-6 message-item" data-claim="<?php echo $msg['is_claim']; ?>" data-tags='<?php echo htmlspecialchars($msg['tags']); ?>'>
                    <div class="card message-card">
                        <?php if ($msg['is_claim']): ?>
                            <div class="claim-badge">
                                <span class="badge badge-warning"><i class="fas fa-hand-paper"></i> Déjà reclamé</span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($msg['image_path'])): ?>
                            <div class="image-container">
                                <img src="<?php echo htmlspecialchars($msg['image_path']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($msg['titre']); ?>">
                                <div class="overlay">
                                    <a href="<?php echo htmlspecialchars($msg['image_path']); ?>" target="_blank" class="btn btn-sm btn-light">
                                        <i class="fas fa-search-plus"></i> Agrandir
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($msg['titre']); ?></h5>
                            
                            <p class="card-text">
                                <small class="text-muted">
                                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($msg['user_name']); ?> | 
                                    <i class="fas fa-clock"></i> <?php echo date('d/m/Y H:i', strtotime($msg['creea'])); ?>
                                </small>
                            </p>
                            
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($msg['content'])); ?></p>
                            
                            <?php if (!empty($msg['ingredients'])): ?>
                                <p><strong>Ingrédients:</strong> <?php echo htmlspecialchars($msg['ingredients']); ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($msg['quantite'])): ?>
                                <p><strong>Quantité:</strong> <?php echo htmlspecialchars($msg['quantite']); ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($msg['nom_adresse']) || !empty($msg['lieu'])): ?>
                                <p>
                                    <?php if (!empty($msg['nom_adresse'])): ?>
                                        <strong>Adresse:</strong> <?php echo htmlspecialchars($msg['nom_adresse']); ?>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($msg['lieu'])): ?>
                                        <?php if (!empty($msg['nom_adresse'])): ?> | <?php endif; ?>
                                        <strong>Lieu de distribution:</strong> <?php echo htmlspecialchars($msg['lieu']); ?>
                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>
                            
                            <?php if (!empty($msg['date_peremption'])): ?>
                                <p>
                                    <strong>Date de péremption:</strong> 
                                    <?php 
                                        $peremption = new DateTime($msg['date_peremption']);
                                        $now = new DateTime();
                                        $diff = $now->diff($peremption);
                                        $isExpired = $now > $peremption;
                                        $badgeClass = $isExpired ? 'danger' : ($diff->days <= 2 ? 'warning' : 'info');
                                        
                                        echo date('d/m/Y', strtotime($msg['date_peremption']));
                                        
                                        if ($isExpired) {
                                            echo ' <span class="badge badge-' . $badgeClass . '">Expiré</span>';
                                        } elseif ($diff->days <= 2) {
                                            echo ' <span class="badge badge-' . $badgeClass . '">Bientôt expiré</span>';
                                        }
                                    ?>
                                </p>
                            <?php endif; ?>
                            
                            <?php if (!empty($msg['tags'])): ?>
                                <div class="mb-3 message-tags">
                                    <?php echo formatTags($msg['tags']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="mt-3">
                                <button type="button" class="btn btn-sm btn-info btn-action" data-toggle="modal" data-target="#editMessageModal<?php echo $msg['id']; ?>">
                                    <i class="fas fa-edit"></i> Modifier
                                </button>
                                <button type="button" class="btn btn-sm btn-danger btn-action" data-toggle="modal" data-target="#deleteMessageModal<?php echo $msg['id']; ?>">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Modal pour modifier l'annonce -->
                <div class="modal fade" id="editMessageModal<?php echo $msg['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $msg['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel<?php echo $msg['id']; ?>">Modifier l'annonce</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form method="POST" action="" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="edit_titre_<?php echo $msg['id']; ?>">Titre <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="edit_titre_<?php echo $msg['id']; ?>" name="titre" value="<?php echo htmlspecialchars($msg['titre']); ?>" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="edit_content_<?php echo $msg['id']; ?>">Description <span class="text-danger">*</span></label>
                                                <textarea class="form-control" id="edit_content_<?php echo $msg['id']; ?>" name="content" rows="3" required><?php echo htmlspecialchars($msg['content']); ?></textarea>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="edit_ingredients_<?php echo $msg['id']; ?>">Ingrédients</label>
                                                <textarea class="form-control" id="edit_ingredients_<?php echo $msg['id']; ?>" name="ingredients" rows="2"><?php echo htmlspecialchars($msg['ingredients']); ?></textarea>
                                                <small class="text-muted">Séparez les ingrédients par des virgules</small>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="edit_quantite_<?php echo $msg['id']; ?>">Quantité</label>
                                                <input type="text" class="form-control" id="edit_quantite_<?php echo $msg['id']; ?>" name="quantite" value="<?php echo htmlspecialchars($msg['quantite']); ?>">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="edit_nom_adresse_<?php echo $msg['id']; ?>">Adresse</label>
                                                <input type="text" class="form-control" id="edit_nom_adresse_<?php echo $msg['id']; ?>" name="nom_adresse" value="<?php echo htmlspecialchars($msg['nom_adresse']); ?>">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="edit_lieu_<?php echo $msg['id']; ?>">Lieu de distribution</label>
                                                <input type="text" class="form-control" id="edit_lieu_<?php echo $msg['id']; ?>" name="lieu" value="<?php echo htmlspecialchars($msg['lieu']); ?>">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="edit_date_peremption_<?php echo $msg['id']; ?>">Date de péremption</label>
                                                <input type="date" class="form-control" id="edit_date_peremption_<?php echo $msg['id']; ?>" name="date_peremption" value="<?php echo !empty($msg['date_peremption']) ? $msg['date_peremption'] : ''; ?>">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="edit_image_<?php echo $msg['id']; ?>">Photo du plat</label>
                                                <input type="file" class="form-control-file" id="edit_image_<?php echo $msg['id']; ?>" name="image" accept="image/*" onchange="previewImage(this, 'edit-preview-image-<?php echo $msg['id']; ?>')">
                                                
                                                <?php if (!empty($msg['image_path'])): ?>
                                                    <div class="mt-2">
                                                        <p>Image actuelle :</p>
                                                        <img src="<?php echo htmlspecialchars($msg['image_path']); ?>" alt="Image actuelle" class="edit-preview-image">
                                                        <button type="button" onclick="deleteImage(<?php echo $msg['id']; ?>)" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i> Supprimer l'image
                                                        </button>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <img id="edit-preview-image-<?php echo $msg['id']; ?>" class="mt-2" style="display: none; max-width: 100%; max-height: 150px;">
                                            </div>
                                            
                                            <?php if ($isSuperAdmin): ?>
                                                <div class="form-group">
                                                    <label for="edit_user_id_<?php echo $msg['id']; ?>">Utilisateur</label>
                                                    <select class="form-control" id="edit_user_id_<?php echo $msg['id']; ?>" name="user_id">
                                                        <?php foreach ($users as $user): ?>
                                                            <option value="<?php echo $user['id']; ?>" <?php echo ($user['id'] == $msg['user_id']) ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($user['pseudo']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="form-check mb-3">
                                                <input type="checkbox" class="form-check-input" id="edit_is_claim_<?php echo $msg['id']; ?>" name="is_claim" <?php echo $msg['is_claim'] ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="edit_is_claim_<?php echo $msg['id']; ?>">
                                                    <span class="badge badge-warning">C'est un plat Déjà reclamé</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group tag-container">
                                        <label>Tags (sélectionnez les tags appropriés)</label>
                                        <div class="row">
                                            <?php 
                                            $currentTags = !empty($msg['tags']) ? json_decode($msg['tags'], true) : [];
                                            foreach ($availableTags as $tag): 
                                            ?>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="<?php echo $tag; ?>" 
                                                               id="edit_tag_<?php echo str_replace(' ', '_', $tag) . '_' . $msg['id']; ?>" 
                                                               name="tags[]"
                                                               <?php echo (is_array($currentTags) && in_array($tag, $currentTags)) ? 'checked' : ''; ?>>
                                                        <label class="form-check-label" for="edit_tag_<?php echo str_replace(' ', '_', $tag) . '_' . $msg['id']; ?>">
                                                            <?php echo $tag; ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Modal de confirmation de suppression -->
                <div class="modal fade" id="deleteMessageModal<?php echo $msg['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $msg['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel<?php echo $msg['id']; ?>">Confirmer la suppression</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>Êtes-vous sûr de vouloir supprimer cette annonce?</p>
                                <p><strong><?php echo htmlspecialchars($msg['titre']); ?></strong></p>
                                <p>Cette action est irréversible.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                <form method="POST" action="">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                    <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Retour en haut de page -->
    <a href="#" id="back-to-top" class="btn btn-primary back-to-top" role="button" style="position: fixed; bottom: 20px; right: 20px; display: none;">
        <i class="fas fa-arrow-up"></i>
    </a>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
    <script>
        function deleteImage(messageId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette image ?')) {
                fetch('delete_image.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'message_id=' + messageId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Erreur lors de la suppression de l\'image');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors de la suppression de l\'image');
                });
            }
        }

        function previewImage(input, previewId) {
            var preview = document.getElementById(previewId);
            
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }
        
        $(document).ready(function() {
            // Afficher/masquer le bouton "Retour en haut"
            $(window).scroll(function() {
                if ($(this).scrollTop() > 300) {
                    $('#back-to-top').fadeIn();
                } else {
                    $('#back-to-top').fadeOut();
                }
            });
            
            // Action du bouton "Retour en haut"
            $('#back-to-top').click(function(e) {
                e.preventDefault();
                $('html, body').animate({scrollTop: 0}, 300);
            });
            
            // Filtrage par recherche
            $('#searchInput').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                filterMessages();
            });
            
            // Filtrage par type (Déjà reclamé/à partager)
            $('#filterType').on('change', function() {
                filterMessages();
            });
            
            // Filtre par tag
            $('.tag-badge').on('click', function() {
                $(this).toggleClass('active');
                filterMessages();
            });
            
            // Réinitialiser les filtres
            $('#resetFilters').on('click', function() {
                $('#searchInput').val('');
                $('#filterType').val('all');
                $('.tag-badge').removeClass('active');
                $('.message-item').show();
            });
            
            // Fonction de filtrage combiné
            function filterMessages() {
                var searchText = $('#searchInput').val().toLowerCase();
                var filterType = $('#filterType').val();
                var selectedTags = [];
                
                $('.tag-badge.active').each(function() {
                    selectedTags.push($(this).data('tag'));
                });
                
                $('.message-item').each(function() {
                    var $item = $(this);
                    var itemText = $item.text().toLowerCase();
                    var isClaim = $item.data('claim') == 1;
                    var itemTags = $item.data('tags');
                    
                    try {
                        itemTags = JSON.parse(itemTags || '[]');
                    } catch (e) {
                        itemTags = [];
                    }
                    
                    var matchesSearch = searchText === '' || itemText.indexOf(searchText) > -1;
                    var matchesType = filterType === 'all' || 
                                     (filterType === 'claim' && isClaim) || 
                                     (filterType === 'share' && !isClaim);
                    
                    var matchesTags = selectedTags.length === 0 || 
                                     selectedTags.every(tag => itemTags.includes(tag));
                    
                    if (matchesSearch && matchesType && matchesTags) {
                        $item.show();
                    } else {
                        $item.hide();
                    }
                });
                
                // Afficher un message si aucun résultat
                if ($('.message-item:visible').length === 0) {
                    if ($('#no-results-message').length === 0) {
                        $('#message-container').append(
                            '<div id="no-results-message" class="col-12 mt-3">' +
                            '<div class="alert alert-warning">' +
                            '<i class="fas fa-exclamation-triangle"></i> Aucune annonce ne correspond à vos critères de recherche.' +
                            '</div>' +
                            '</div>'
                        );
                    }
                } else {
                    $('#no-results-message').remove();
                }
            }
            
            // Confirmation avant soumission des formulaires de suppression
            $('form[action="delete"]').on('submit', function(e) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer cette annonce? Cette action est irréversible.')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>

                
