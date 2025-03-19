<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clichy | AntiGaspi</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
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
$isnotAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] == 0;
?>

<?php include 'templates/header.php'; ?>

<?php include 'templates/adminmessage.php'; ?>
<?php include 'templates/clientmessage.php'; ?>

<div class="container">
   <nav class="nav-header">
    <a href="annonces.php" class="filter-btn">Filtrer par tags</a>
    <?php if ($isSuperAdmin): ?>
        <a href="superadmin.php" class="admin-btn">Gestion des comptes</a> 
        <a href="superadminanonce.php" class="admin-btn">Gestion des annonces</a>
        <a href="resetclaim.php" class="admin-btn">Reset des claims</a>
    <?php endif; ?>
    </nav>

    

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
                    echo '<a href="claim.php?id=' . $row['message_id'] . '" class="claim-link">Commander</a>';
                } else {
                    echo '<p class="claimed-message">Cette annonce a déjà été réclamée!</p>';
                }


                if ($row['user_id'] == $userId) {
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

<?php include 'templates/footer.php'; ?>
</body>
</html>