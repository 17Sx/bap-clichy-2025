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

require_once 'vendor/autoload.php';

$messageId = $_GET['id'];
$dsn = 'mysql:host=localhost;dbname=renduphpcrud;charset=utf8';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   
    // Récupération du message et de l'expéditeur
    $stmt = $pdo->prepare("SELECT m.*, u.email, u.pseudo FROM message m JOIN user u ON m.user_id = u.id WHERE m.id = ?");
    $stmt->execute([$messageId]);
    $messageInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$messageInfo || $messageInfo['is_claim']) {
        die("Message introuvable ou déjà réclamé.");
    }
   
    if (empty($messageInfo['email']) || !filter_var($messageInfo['email'], FILTER_VALIDATE_EMAIL)) {
        die("Adresse email invalide.");
    }
    
    $stmtClaimer = $pdo->prepare("SELECT pseudo, email FROM user WHERE id = ?");
    $stmtClaimer->execute([$_SESSION['user_id']]);
    $claimer = $stmtClaimer->fetch(PDO::FETCH_ASSOC);
    
    if (!$claimer) {
        die("Erreur: utilisateur non trouvé.");
    }
    
    // Marquer comme réclamé
    $stmt = $pdo->prepare("UPDATE message SET is_claim = 1 WHERE id = ?");
    $stmt->execute([$messageId]);
    
    $titre = !empty($messageInfo['titre']) ? $messageInfo['titre'] : $messageInfo['content'];
    
    // Envoi d'email via Brevo API
    $apiKey = getenv('BREVO_API_KEY') ?: '';
    
    $emailData = [
        "sender" => ["name" => "Clichy Anti-Gaspi", "email" => "antigaspi@clichy.fr"],
        "to" => [["email" => $messageInfo['email'], "name" => $messageInfo['pseudo']]],
        "subject" => "Votre annonce a été réclamée",
        "htmlContent" => "<p>Bonjour {$messageInfo['pseudo']},</p><p>Votre annonce \"{$titre}\" a été réclamée par {$claimer['pseudo']}.</p><p>Cordialement,<br>L'équipe Clichy Anti-Gaspi</p>"
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.brevo.com/v3/smtp/email");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "accept: application/json",
        "api-key: $apiKey",
        "content-type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 201) {
        die("Erreur d'envoi d'email: " . htmlspecialchars($response));
    }
    
    header('Location: index.php?success=email_sent');
    exit();
    
} catch (PDOException $e) {
    die('Erreur de base de données: ' . $e->getMessage());
} catch (Exception $e) {
    die('Une erreur est survenue: ' . $e->getMessage());
}
?>
