<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Vérifier si l'utilisateur est admin
if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1) {
    header('Location: index.php?error=admin_cannot_claim');
    exit();
}

if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $messageId = $_POST['id'];
} elseif (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $messageId = $_GET['id'];
} else {
    header('Location: index.php');
    exit();
}

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
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envoi d'email en cours...</title>
    <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/claim.css">
</head>

<body>
    <div class="email-container">

        <img src="public/img/logo.png" alt="Logo" class="logo">

        <div id="loading">
            <div class="loader"></div>
            <div class="status-message">Envoi de l'email en cours...</div>
        </div>

        <div id="success" class="success">
            <div class="icon">✓</div>
            <div class="status-message">Email envoyé avec succès!</div>
            <div>Vous allez être redirigé dans un instant...</div>
        </div>

        <div id="error" class="error">
            <div class="icon">✕</div>
            <div class="status-message">Erreur lors de l'envoi de l'email</div>
            <div id="error-details"></div>
        </div>
    </div>

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

                document.getElementById('loading').style.display = 'none';

                const successElement = document.getElementById('success');
                successElement.style.display = 'block';
                successElement.classList.add('fade-in');

                setTimeout(function() {
                    window.location.href = 'index.php?success=email_sent';
                }, 2000);
            }, function(error) {
                console.log('Erreur lors de l\'envoi de l\'email:', error);

                document.getElementById('loading').style.display = 'none';

                const errorElement = document.getElementById('error');
                errorElement.style.display = 'block';
                errorElement.classList.add('fade-in');
                document.getElementById('error-details').textContent = error.text || 'Veuillez réessayer plus tard';
            });
    </script>
</body>

</html>