<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clichy | S'enregistrer</title>
    <link rel="stylesheet" href="css/register.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
      integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <style>
        .password-container {
            position: relative;
        }
        #togglePassword {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }
    </style>
</head>
<body>
<header>
    <img src="public/img/logo.png" alt="">
</header>
<div class="img-container">
    <img src="public/img/registerleft.png" alt="">
</div>
<div class="register-container">
    <h2>Créer votre compte</h2>
    <form action="register.php" method="post">
        <input type="text" id="pseudo" name="pseudo" placeholder="Nom" required><br><br>
       
        <input type="email" id="email" name="email" placeholder="Email" required><br><br>
       
        <div class="password-container">
            <input type="password" id="password" name="password" placeholder="Mot de passe" required>
            <i class="fas fa-eye" id="togglePassword"></i>
        </div><br><br>
       
        <input type="submit" value="S'enregistrer">
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const togglePasswordIcon = document.getElementById('togglePassword');

        togglePasswordIcon.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    });
</script>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $pseudo = isset($_POST['pseudo']) ? $_POST['pseudo'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        if (empty($pseudo) || empty($email) || empty($password)) {
            echo "Tous les champs sont requis.";
            exit;
        }
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $conn = new PDO("mysql:host=localhost;dbname=RenduPhpCRUD", 'root', '');
        $sql = "INSERT INTO user (pseudo, email, password) VALUES (:pseudo, :email, :password)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':pseudo', $pseudo);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        if ($stmt->execute()) {
            echo "Nouvel enregistrement créé avec succès";
        } else {
            echo "Erreur : " . $stmt->errorInfo()[2];
        }
    }
    ?>
</body>
</html>