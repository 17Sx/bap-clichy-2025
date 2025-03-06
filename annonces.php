<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clichy | AntiGaspi - Filtrer par tags</title>
    <link rel="stylesheet" href="css/global.css">
    <style>
        .filter-section {
            margin: 20px 0;
            padding: 15px;
            background: #f8f8f8;
            border-radius: 5px;
        }
        
        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .tag-button {
            padding: 6px 12px;
            border: 1px solid #ddd;
            border-radius: 20px;
            background: white;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .tag-button.active {
            background: #4CAF50;
            color: white;
            border-color: #4CAF50;
        }
        
        .message-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .message-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            background: white;
        }
        
        .message-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        
        .tag-list {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 10px;
        }
        
        .tag-pill {
            background: #f1f1f1;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8em;
        }
    </style>
</head>
<body>

<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$pseudo = $_SESSION['pseudo'];
$userId = $_SESSION['user_id'];
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] == 1;

$dsn = 'mysql:host=localhost;dbname=renduphpcrud;charset=utf8';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Définition de tous les tags disponibles
    $allTags = [
        'légumes', 'fruits', 'viande', 'poisson', 'produits laitiers', 
        'bio', 'gratuit', 'à petit prix', 'fait maison', 'végétarien', 
        'vegan', 'sans gluten'
    ];

    // Récupérer le tag sélectionné
    $selectedTag = isset($_GET['tag']) ? $_GET['tag'] : '';
    
?>

<div class="container">
    <nav class="nav-header">
        <a href="index.php" class="home-btn">Accueil</a>
        <a href="logout.php" class="logout-btn">Déconnexion</a>
    </nav>

    <div class="filter-section">
        <h2>Filtrer par tags</h2>
        <div class="filter-form">
            <a href="annonces.php" class="tag-button <?php echo empty($selectedTag) ? 'active' : ''; ?>">
                Tous
            </a>
            <?php foreach ($allTags as $tag): ?>
            <a href="annonces.php?tag=<?php echo urlencode($tag); ?>" 
               class="tag-button <?php echo $selectedTag === $tag ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($tag); ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="messages-section">
        <h2><?php echo empty($selectedTag) ? 'Toutes les annonces' : 'Annonces avec le tag "' . htmlspecialchars($selectedTag) . '"'; ?></h2>
        
        <div class="message-grid">
            <?php
            // Construire la requête SQL en fonction du tag sélectionné
            $sql = "SELECT user.pseudo, message.*, message.id AS message_id, message.user_id
                    FROM message
                    JOIN user ON message.user_id = user.id";
            
            if (!empty($selectedTag)) {
                $sql .= " WHERE JSON_CONTAINS(tags, '\"" . $selectedTag . "\"', '$')";
            }
            
            $sql .= " ORDER BY message.creea DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $isOwnMessage = $row['user_id'] == $userId;
                $isClaimed = $row['is_claim'] == 1;
                $messageClass = $isClaimed ? 'claimed' : '';
                
                // Convertir les tags JSON en tableau PHP
                $tags = json_decode($row['tags'], true) ?? [];
                
                echo '<div class="message-card ' . $messageClass . '">';
                
                // Titre de l'annonce
                echo '<h3>' . htmlspecialchars($row['titre'] ?? 'Sans titre') . '</h3>';
                
                // Image
                if (!empty($row['image_path'])) {
                    echo '<img src="' . htmlspecialchars($row['image_path']) . '" alt="image" class="message-image">';
                }
                
                // Méta-informations
                echo '<p class="message-meta">' . htmlspecialchars($row['pseudo']) . ' - ' . htmlspecialchars($row['creea']) . '</p>';
                
                // Contenu
                echo '<p class="message-content">' . htmlspecialchars($row['content']) . '</p>';
                
                // Informations supplémentaires
                if (!empty($row['ingredients'])) {
                    echo '<p><strong>Ingrédients:</strong> ' . nl2br(htmlspecialchars($row['ingredients'])) . '</p>';
                }
                
                if (!empty($row['quantite'])) {
                    echo '<p><strong>Quantité:</strong> ' . htmlspecialchars($row['quantite']) . '</p>';
                }
                
                if (!empty($row['nom_adresse'])) {
                    echo '<p><strong>Contact:</strong> ' . htmlspecialchars($row['nom_adresse']) . '</p>';
                }
                
                // Afficher les tags
                if (!empty($tags)) {
                    echo '<div class="tag-list">';
                    foreach ($tags as $tag) {
                        echo '<span class="tag-pill">' . htmlspecialchars($tag) . '</span>';
                    }
                    echo '</div>';
                }
                
                // Bouton pour réclamer
                if (!$isClaimed) {
                    echo '<a href="claim.php?id=' . $row['message_id'] . '" class="claim-link">Réclamer l\'annonce</a>';
                } else {
                    echo '<p class="claimed-message">Cette annonce a déjà été réclamée!</p>';
                }
                
                // Contrôles d'administration
                if ($isAdmin) {
                    echo '<div class="admin-controls">';
                    echo '<a href="edit.php?id=' . $row['message_id'] . '" class="edit-link">Modifier</a>';
                    echo '<a href="delete.php?id=' . $row['message_id'] . '" class="delete-link">Supprimer</a>';
                    echo '</div>';
                }
                
                echo '</div>';
            }
            
            ?>
        </div>
    </div>
</div>

<?php
} catch (PDOException $e) {
    echo 'Erreur : ' . $e->getMessage();
}
?>

<footer>
    <div class="footer-left">
        <img src="public/img/logoblanc.png" alt="">
        <div class="footer-text">
            <p>Les restes d'aujourd'hui,</p>
            <p>les repas de demains</p>
        </div>
    </div>

    <div class="footer-contact">
        <h3>Contact</h3>
        <div class="footer-contact-text">
            <p>Mairie de Clichy-la-Garenne 80,</p>
            <p>Boulevard Jean Jaurès </p>
            <p>92110 Clichy </p>
            <p>01 47 15 30 00</p>
        </div>
    </div>
</footer>

</body>
</html>