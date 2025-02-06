<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['admin'] != 1) {
    echo "Accès refusé.";
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

        $stmt = $pdo->prepare("DELETE FROM message WHERE id = :id");
        $stmt->bindParam(':id', $message_id);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            echo "non non non";
        }
    } catch (PDOException $e) {
        echo 'Erreur : ' . $e->getMessage();
    }
} else {
    echo "nop";
}
?>
