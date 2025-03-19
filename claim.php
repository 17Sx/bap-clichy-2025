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
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envoi d'email en cours...</title>
    <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        .email-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
            width: min(90%, 500px);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .status-message {
            margin: 1.5rem 0;
            font-size: 1.2rem;
            font-weight: 400;
        }

        .loader {
            display: inline-block;
            width: 60px;
            height: 60px;
            margin-bottom: 1rem;
            border: 4px solid #f3f3f3;
            border-radius: 50%;
            border-top: 4px solid #4CAF50;
            animation: spin 1.5s linear infinite;
        }

        .success {
            color: #4CAF50;
            font-weight: 500;
            display: none;
        }

        .error {
            color: #e74c3c;
            font-weight: 500;
            display: none;
        }

        .icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        .logo {
            max-width: 150px;
            margin-bottom: 1.5rem;
        }
    </style>
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

