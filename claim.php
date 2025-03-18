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
   
    $stmt = $pdo->prepare("SELECT m.*, u.email, u.pseudo FROM message m JOIN user u ON m.user_id = u.id WHERE m.id = ?");
    $stmt->execute([$messageId]);
    $messageInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$messageInfo) {
        die("Message non trouvé.");
    }
   
    if (empty($messageInfo['email']) || !filter_var($messageInfo['email'], FILTER_VALIDATE_EMAIL)) {
        die("Adresse email du createur de l'anonce invalide.");
    }
    
    $stmtClaimer = $pdo->prepare("SELECT pseudo, email FROM user WHERE id = ?");
    $stmtClaimer->execute([$_SESSION['user_id']]);
    $claimer = $stmtClaimer->fetch(PDO::FETCH_ASSOC);
    
    if (!$claimer) {
        die("Erreur: utilisateur non trouvé.");
    }
    
    $stmt = $pdo->prepare("UPDATE message SET is_claim = 1 WHERE id = ?");
    $stmt->execute([$messageId]);
    
    $titre = !empty($messageInfo['titre']) ? $messageInfo['titre'] : $messageInfo['content'];
    
    $emailData = [
        'recipient_email' => $messageInfo['email'],
        'recipient_name' => $messageInfo['pseudo'],
        'claimer_name' => $claimer['pseudo'],
        'claimer_email' => $claimer['email'],
        'post_title' => $titre
    ];
    
    $emailDataJson = json_encode($emailData);
    
} catch (PDOException $e) {
    die('Erreur bdd: ' . $e->getMessage());
} catch (Exception $e) {
    die('Une erreur est survenue: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Envoi d'email en cours...</title>
    <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
</head>
<body>
    <div id="status">Envoi de l'email en cours...</div>

    <script>
        (function() {
            emailjs.init("5x47-b48NAf_qH1o9");
        })();
        
        const emailData = <?php echo $emailDataJson; ?>;
        
        const templateParams = {
            recipient_email: emailData.recipient_email,
            recipient_name: emailData.recipient_name,
            claimer_name: emailData.claimer_name,
            claimer_email: emailData.claimer_email,
            post_title: emailData.post_title
        };
        
        emailjs.send('service_yobf3r3', 'template_21h431h', templateParams)
            .then(function(response) {
                console.log('Email envoyé avec succès!', response);
                document.getElementById('status').innerHTML = 'Email envoyé avec succès!';
                setTimeout(function() {
                    window.location.href = 'index.php?success=email_sent';
                }, 1500);
            }, function(error) {
                console.log('Erreur lors de l\'envoi de l\'email:', error);
                document.getElementById('status').innerHTML = 'Erreur lors de l\'envoi de l\'email: ' + error.text;
            });
    </script>
</body>
</html>
