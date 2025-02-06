<?php
session_start();

if (!isset($_SESSION['user_id']) || empty($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    echo "non";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id']) && !empty($_POST['content'])) {
    $message_id = $_POST['id'];
    $new_content = $_POST['content'];

    $dsn = 'mysql:host=localhost;dbname=renduphpcrud;charset=utf8';
    $username = 'root';
    $password = '';

    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("UPDATE message SET content = :content WHERE id = :id");
    $stmt->execute([':content' => $new_content, ':id' => $message_id]);

    header("Location: index.php");
    exit();
}

echo 'rien';
?>