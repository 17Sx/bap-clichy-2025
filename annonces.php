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
        
        .filter-controls {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .filter-control {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-control select {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        
        .filter-control label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
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

    $allTags = [
        'légumes', 'fruits', 'viande', 'poisson', 'produits laitiers', 
        'bio', 'gratuit', 'à petit prix', 'fait maison', 'végétarien', 
        'vegan', 'sans gluten'
    ];

    $selectedTag = isset($_GET['tag']) ? $_GET['tag'] : '';
    $selectedLieu = isset($_GET['lieu']) ? $_GET['lieu'] : '';
    $sortDate = isset($_GET['sort_date']) ? $_GET['sort_date'] : '';
    
    $lieuStmt = $pdo->query("SELECT DISTINCT lieu FROM message WHERE lieu IS NOT NULL ORDER BY lieu");
    $lieux = $lieuStmt->fetchAll(PDO::FETCH_COLUMN);
    
?>

<div class="container">
    <nav class="nav-header">
        <a href="index.php" class="home-btn">Accueil</a>
        <a href="logout.php" class="logout-btn">Déconnexion</a>
    </nav>

    <div class="filter-section">
        <h2>Filtrer les annonces</h2>
        
        <!-- Filtres supplémentaires: lieu et date de péremption -->
        <div class="filter-controls">
            <div class="filter-control">
                <label for="lieu">Filtrer par lieu:</label>
                <select id="lieu" name="lieu" onchange="applyFilters()">
                    <option value="">Tous les lieux</option>
                    <?php foreach ($lieux as $lieu): ?>
                        <option value="<?php echo htmlspecialchars($lieu); ?>" 
                                <?php echo $selectedLieu === $lieu ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($lieu); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-control">
                <label for="sort_date">Trier par date de péremption:</label>
                <select id="sort_date" name="sort_date" onchange="applyFilters()">
                    <option value="">Sans tri</option>
                    <option value="asc" <?php echo $sortDate === 'asc' ? 'selected' : ''; ?>>
                        Du plus récent au plus ancien
                    </option>
                    <option value="desc" <?php echo $sortDate === 'desc' ? 'selected' : ''; ?>>
                        Du plus ancien au plus récent
                    </option>
                </select>
            </div>
        </div>
        
        <div class="filter-form">
            <a href="javascript:void(0)" onclick="selectTag('')" class="tag-button <?php echo empty($selectedTag) ? 'active' : ''; ?>">
                Tous
            </a>
            <?php foreach ($allTags as $tag): ?>
            <a href="javascript:void(0)" onclick="selectTag('<?php echo $tag; ?>')" 
               class="tag-button <?php echo $selectedTag === $tag ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($tag); ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="messages-section">
        <h2>
            <?php 
            $title = 'Annonces';
            if (!empty($selectedTag)) {
                $title .= ' avec le tag "' . htmlspecialchars($selectedTag) . '"';
            }
            if (!empty($selectedLieu)) {
                $title .= ' à ' . htmlspecialchars($selectedLieu);
            }
            echo $title;
            ?>
        </h2>
        
        <div class="message-grid">
            <?php
            // Construire la requête SQL en fonction des filtres sélectionnés
            $sql = "SELECT user.pseudo, message.*, message.id AS message_id, message.user_id
                    FROM message
                    JOIN user ON message.user_id = user.id
                    WHERE 1=1";
            
            $params = [];
            
            if (!empty($selectedTag)) {
                $sql .= " AND JSON_CONTAINS(tags, :tag, '$')";
                $params[':tag'] = json_encode($selectedTag);
            }
            
            if (!empty($selectedLieu)) {
                $sql .= " AND lieu = :lieu";
                $params[':lieu'] = $selectedLieu;
            }
            
            // Ordre de tri
            if (!empty($sortDate)) {
                $sql .= " ORDER BY date_peremption " . ($sortDate === 'asc' ? 'ASC' : 'DESC');
            } else {
                $sql .= " ORDER BY message.creea DESC";
            }
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            $messageCount = 0;
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $messageCount++;
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
                
                // Afficher le lieu de collecte
                if (!empty($row['lieu'])) {
                    echo '<p><strong>Lieu de collecte:</strong> ' . htmlspecialchars($row['lieu']) . '</p>';
                }
                
                // Afficher la date de péremption
                if (!empty($row['date_peremption'])) {
                    echo '<p><strong>Date de péremption:</strong> ' . htmlspecialchars($row['date_peremption']) . '</p>';
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
            
            if ($messageCount === 0) {
                echo '<p>Aucune annonce ne correspond aux critères sélectionnés.</p>';
            }
            
            ?>
        </div>
    </div>
</div>

<script>
    function applyFilters() {
        const tag = '<?php echo $selectedTag; ?>';
        const lieu = document.getElementById('lieu').value;
        const sortDate = document.getElementById('sort_date').value;
        
        let url = 'annonces.php?';
        
        if (tag) {
            url += 'tag=' + encodeURIComponent(tag) + '&';
        }
        
        if (lieu) {
            url += 'lieu=' + encodeURIComponent(lieu) + '&';
        }
        
        if (sortDate) {
            url += 'sort_date=' + encodeURIComponent(sortDate);
        }
        
        // Supprimer le dernier & si nécessaire
        if (url.endsWith('&')) {
            url = url.slice(0, -1);
        }
        
        window.location.href = url;
    }
    
    function selectTag(tag) {
        const lieu = document.getElementById('lieu').value;
        const sortDate = document.getElementById('sort_date').value;
        
        let url = 'annonces.php?';
        
        if (tag) {
            url += 'tag=' + encodeURIComponent(tag) + '&';
        }
        
        if (lieu) {
            url += 'lieu=' + encodeURIComponent(lieu) + '&';
        }
        
        if (sortDate) {
            url += 'sort_date=' + encodeURIComponent(sortDate);
        }
        
        // Supprimer le dernier & si nécessaire
        if (url.endsWith('&')) {
            url = url.slice(0, -1);
        }
        
        window.location.href = url;
    }
</script>

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