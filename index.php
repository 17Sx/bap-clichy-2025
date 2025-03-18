<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clichy | AntiGaspi</title>
    <link rel="stylesheet" href="css/global.css">
</head>
<body>

<?php
session_start();

if (!isset($_SESSION['csrf_token']) || empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$pseudo = $_SESSION['pseudo'];
$userId = $_SESSION['user_id'];
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] == 1;
$isSuperAdmin = isset($_SESSION['is_superadmin']) && $_SESSION['is_superadmin'] == 1;
?>

<div class="container">
   <nav class="nav-header">
    <a href="annonces.php" class="filter-btn">Filtrer par tags</a>
    <a href="logout.php" class="logout-btn">Déconnexion</a>
    <?php if ($isSuperAdmin): ?>
        <a href="superadmin.php" class="admin-btn">Gestion des comptes</a> 
        <a href="superadminanonce.php" class="admin-btn">Gestion des annonces</a>
        <a href="resetclaim.php" class="admin-btn">Reset des claims</a>
    <?php endif; ?>
    </nav>

    
    <?php if ($isAdmin): ?>
    <div class="message-form">
        <h2>Créer une nouvelle annonce anti gaspi!</h2>
        <form action="create.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="form-group">
                <label for="titre">Titre de l'annonce:</label>
                <input type="text" name="titre" id="titre" placeholder="Titre de votre annonce" required>
            </div>
            
            <div class="form-group">
                <label for="content">Description:</label>
                <textarea name="content" id="content" placeholder="Écrivez votre annonce ici, <?php echo htmlspecialchars($pseudo); ?>" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="ingredients">Ingrédients:</label>
                <textarea name="ingredients" id="ingredients" placeholder="Listez les ingrédients, un par ligne"></textarea>
            </div>
            
            <div class="form-group">
                <label for="quantite">Quantité:</label>
                <input type="text" name="quantite" id="quantite" placeholder="Ex: 500g, 2 portions, etc.">
            </div>
            
            <div class="form-group">
                <label for="nom_adresse">Nom et adresse:</label>
                <input type="text" name="nom_adresse" id="nom_adresse" placeholder="Votre nom et adresse">
            </div>
            
            <div class="form-group">
                <label for="lieu">Lieu de collecte:</label>
                <select name="lieu" id="lieu" required>
                    <option value="Lycée de Paris">Lycée de Paris</option>
                    <option value="Lycée de Boulogne">Lycée de Boulogne</option>
                    <option value="Primaire de Garches">Primaire de Garches</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="date_peremption">Date de péremption:</label>
                <input type="date" name="date_peremption" id="date_peremption" required>
            </div>
            
            <div class="form-group">
                <label for="tags">Tags (sélectionnez plusieurs options avec Ctrl+clic ou Cmd+clic):</label>
                <select name="tags[]" id="tags" multiple class="form-control">
                    <option value="légumes">Légumes</option>
                    <option value="fruits">Fruits</option>
                    <option value="viande">Viande</option>
                    <option value="poisson">Poisson</option>
                    <option value="produits laitiers">Produits laitiers</option>
                    <option value="bio">Bio</option>
                    <option value="gratuit">Gratuit</option>
                    <option value="à petit prix">À petit prix</option>
                    <option value="fait maison">Fait maison</option>
                    <option value="végétarien">Végétarien</option>
                    <option value="vegan">Vegan</option>
                    <option value="sans gluten">Sans gluten</option>
                </select>
            </div>

            
            <div class="form-group">
                <label for="image">Image:</label>
                <input type="file" name="image" id="image" accept="image/*">
            </div>
            
            <button type="submit">Envoyer l'annonce</button>
        </form>
    </div>
<?php endif; ?>

    <div class="messages-section">
        <h2>Annonces les plus récentes</h2>
        <div id="messages">
            <?php
            $dsn = 'mysql:host=localhost;dbname=renduphpcrud;charset=utf8';
            $username = 'root';
            $password = '';

            try {
                $pdo = new PDO($dsn, $username, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $stmt = $pdo->query("
                SELECT user.pseudo, 
                       message.content, 
                       message.creea, 
                       message.id AS message_id, 
                       message.user_id, 
                       message.image_path, 
                       message.is_claim,
                       message.titre,
                       message.ingredients,
                       message.tags,
                       message.quantite,
                       message.nom_adresse,
                       message.lieu,
                       message.date_peremption
                FROM message
                JOIN user ON message.user_id = user.id
                ORDER BY message.creea DESC
                LIMIT 10
            ");
            

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $isOwnMessage = $row['user_id'] == $userId;
                $isClaim= $row['is_claim'] == 1;
                $messageClass = $isOwnMessage ? 'own-message' : 'other-message';

                if ($isClaim) {
                    $messageClass .= ' claimed';
                }
                
                echo '<div class="message ' . $messageClass . '">';
                
                if (!empty($row['titre'])) {
                    echo '<h3>' . htmlspecialchars($row['titre']) . '</h3>';
                }
                
                if (!empty($row['image_path'])) {
                    echo '<img src="' . htmlspecialchars($row['image_path']) . '" alt="image" class="message-image">';
                }
                
                echo '<p class="message-content">' . htmlspecialchars($row['content']) . '</p>';
                
                echo '<div class="message-details">';
                
                if (!empty($row['ingredients'])) {
                    echo '<div class="detail-item"><strong>Ingrédients:</strong><br>';
                    echo nl2br(htmlspecialchars($row['ingredients']));
                    echo '</div>';
                }
                
                if (!empty($row['quantite'])) {
                    echo '<div class="detail-item"><strong>Quantité:</strong> ' . htmlspecialchars($row['quantite']) . '</div>';
                }
                
                if (!empty($row['nom_adresse'])) {
                    echo '<div class="detail-item"><strong>Contact:</strong> ' . htmlspecialchars($row['nom_adresse']) . '</div>';
                }
                
                if (!empty($row['tags'])) {
                    $tagsArray = json_decode($row['tags'], true);
                    if (is_array($tagsArray) && !empty($tagsArray)) {
                        echo '<div class="detail-item"><strong>Tags:</strong>';
                        echo '<div class="message-tags">';
                        foreach ($tagsArray as $tag) {
                            echo '<span class="tag">' . htmlspecialchars($tag) . '</span>';
                        }
                        echo '</div></div>';
                    }
                }
                
                echo '<p class="message-meta"> crée par ' . htmlspecialchars($row['pseudo']) . ' le ' . htmlspecialchars($row['creea']) . '</p>';

                
                echo '</div>'; 
                
                if (!$row['is_claim']) {
                    echo '<a href="claim.php?id=' . $row['message_id'] . '" class="claim-link">Réclamer l\'annonce</a>';
                } else {
                    echo '<p class="claimed-message">Cette annonce a déjà été réclamée!</p>';
                }


                if ($isAdmin) {
                    echo '<div class="admin-controls">';
                    echo '<a href="edit.php?id=' . $row['message_id'] . '" class="edit-link">Modifier</a>';
                    echo '<a href="delete.php?id=' . $row['message_id'] . '" class="delete-link">Supprimer</a>';
                    echo '</div>';
                }
                echo '</div>'; 
            }
            
            } catch (PDOException $e) {
                echo 'Erreur : ' . $e->getMessage();
            }
            ?>
        </div>
    </div>
</div>


<footer>

    <div class="footer-left">
        <img src="public/img/logoblanc.png" alt="">
        <div class="footer-text">
            <p>Les restes d'aujourd'hui,</p>
            <p>les repas de demains</p>
        </div>
    </div>

    <div class="footer-contact">
        <h3>
            Contact
        </h3>

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