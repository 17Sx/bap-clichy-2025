<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clichy | Connexion</title>
    <link rel="stylesheet" href="css/login.css">
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
        <img src="public/img/loginleft.png" alt="">
    </div>
    <div class="login-container">
        <h1>Se connecter</h1>
        <form action="login.php" method="POST">
            <input type="text" name="pseudo" id="pseudo" placeholder="Nom d'utilisateur" required>
            <div class="password-container">
                <input class="password" type="password" name="password" id="password" placeholder="Mot de passe" required>
                <i class="fas fa-eye" id="togglePassword"></i>
            </div>
            <button type="submit">Confirmer</button>
        </form>
        <p>Veuillez demander un accès à un admin pour vous créer un compte, <br>a l'adresse mail suivante : bapclichy@gmail.com</p>
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
    session_start();
    require_once 'bdd.php';
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST['pseudo']) && !empty($_POST['password'])) {
            $pseudo = $_POST['pseudo'];
            $password = $_POST['password'];
            $stmt = $connexion->prepare("SELECT id, pseudo, password, admin, is_superadmin FROM user WHERE pseudo = :pseudo");
            $stmt->bindParam(':pseudo', $pseudo);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['pseudo'] = $user['pseudo'];
                $_SESSION['admin'] = $user['admin'];
                $_SESSION['is_superadmin'] = $user['is_superadmin'];
                header("Location: index.php");
                exit();
            } else {
                echo "<script>alert('Mauvais identifiants')</script>";
            }
        } else {
            $error = "remplir tous les champs";
        }
    }
    ?>
</body>
</html>