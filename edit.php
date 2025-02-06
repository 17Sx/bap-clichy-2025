<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Whatassap</title>
 </head>
<body>

<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['admin'] != 1) {
    echo '<div class="error">Accès refusé.</div>';
    exit();
}

    if (isset($_GET['id'])) {
        $message_id = $_GET['id'];
        $dsn = 'mysql:host=localhost;dbname=renduphpcrud;charset=utf8';
        $username = 'root';
        $password = '';

        try {
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare("SELECT content FROM message WHERE id = :id");
            $stmt->bindParam(':id', $message_id);
            $stmt->execute();
            $message = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($message) {
            echo '<div class="message-container">';
            echo '<h2>Modifier le message</h2>';
            echo '<form action="update.php" method="POST">';
            echo '<input type="hidden" name="id" value="' . htmlspecialchars($message_id) . '">';
            echo '<textarea name="content" rows="5">' . htmlspecialchars($message['content']) . '</textarea>';
            echo '<button type="submit">Mettre à jour</button>';
            echo '</form>';
            echo '</div>';
        } else {
            echo '<div class="error">no msg</div>';
        }
    } catch (PDOException $e) {
        echo '<div class="error">Erreur : ' . $e->getMessage() . '</div>';
    }
} else {
    echo '<div class="error">noid</div>';
}
?>
</body>
</html>
