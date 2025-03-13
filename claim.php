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
$dsn = 'mysql:host=localhost;dbname=renduphpcrud;charset=utf8';
$username = 'root';
$password = '';
try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("
        SELECT m.content, m.user_id, u.email, u.pseudo
        FROM message m
        JOIN user u ON m.user_id = u.id
        WHERE m.id = ?
    ");
    $stmt->execute([$messageId]);
    $messageInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stmtClaimer = $pdo->prepare("SELECT pseudo FROM user WHERE id = ?");
    $stmtClaimer->execute([$_SESSION['user_id']]);
    $claimer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->prepare("UPDATE message SET is_claim = 1 WHERE id = ?");
    $stmt->execute([$messageId]);
    
    $to = $messageInfo['email'];
    $subject = "Votre annonce a été réclamée";
    
    $message = "
    <html>
    <head>
        <title>Votre annonce a été réclamée</title>
    </head>
    <body>
        <p>Bonjour {$messageInfo['pseudo']},</p>
        <p>Votre annonce \"{$messageInfo['content']}\" a été réclamée par {$claimer['pseudo']}.</p>
        <p>Cordialement,<br>
        L'équipe Clichy Anti-Gaspi</p>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: antigaspi@clichy.fr" . "\r\n";
    $headers .= "Reply-To: antigaspi@clichy.fr" . "\r\n";
    
    mail($to, $subject, $message, $headers);
    
    header('Location: index.php');
    exit();
} catch (PDOException $e) {
    echo 'Erreur : ' . $e->getMessage();
}
?>