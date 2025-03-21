<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$messageId = $_GET['id'];
$userId = $_SESSION['user_id'];
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] == 1;

try {
    $dsn = 'mysql:host=localhost;dbname=renduphpcrud;charset=utf8';
    $username = 'root';
    $password = '';
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifier si l'utilisateur est le propriétaire ou un admin
    $stmt = $pdo->prepare("SELECT user_id FROM message WHERE id = ?");
    $stmt->execute([$messageId]);
    $message = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$message) {
        header('Location: index.php?error=message_not_found');
        exit();
    }

    if ($message['user_id'] != $userId && !$isAdmin) {
        header('Location: index.php?error=unauthorized');
        exit();
    }

    // Supprimer le message
    $stmt = $pdo->prepare("DELETE FROM message WHERE id = ?");
    $stmt->execute([$messageId]);

    header('Location: index.php?success=message_deleted');
    exit();
} catch (PDOException $e) {
    header('Location: index.php?error=database_error');
    exit();
}
