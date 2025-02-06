<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Whatassap</title>
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
?>

<div class="container">
    <nav class="nav-header">
        <a href="logout.php" class="logout-btn">Déconnexion</a>
    </nav>

    <div class="message-form">
        <h2>Créer un nouveau message</h2>
        <form action="create.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <textarea name="content" placeholder="Écrivez votre message ici, <?php echo htmlspecialchars($pseudo); ?>" required></textarea>
            <input type="file" name="image" accept="image/*">   
            <button type="submit">Envoyer le message</button>
        </form>
    </div>

    <div class="messages-section">
        <h2>Messages récents</h2>
        <div id="messages">
            <?php
            $dsn = 'mysql:host=localhost;dbname=renduphpcrud;charset=utf8';
            $username = 'root';
            $password = '';

            try {
                $pdo = new PDO($dsn, $username, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->query("
                SELECT user.pseudo, message.content, message.creea, message.id AS message_id, 
                    message.user_id, message.image_path
                FROM message
                JOIN user ON message.user_id = user.id
                ORDER BY message.creea DESC
                LIMIT 10
            ");

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $isOwnMessage = $row['user_id'] == $userId;
                $messageClass = $isOwnMessage ? 'own-message' : 'other-message';
                echo '<div class="message ' . $messageClass . '">';
                
                // Afficher l'image seulement si elle existe
                if (!empty($row['image_path'])) {
                    echo '<img src="' . htmlspecialchars($row['image_path']) . '" alt="image" class="message-image">';
                }
                
                echo '<p class="message-meta">' . htmlspecialchars($row['pseudo']) . ' - ' . htmlspecialchars($row['creea']) . '</p>';
                echo '<p class="message-content">' . htmlspecialchars($row['content']) . '</p>';
            
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

</body>
</html>
