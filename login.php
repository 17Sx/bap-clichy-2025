<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
</head>
<body>

<div>
    <div class="login-container">
        <h1>CONNEXION</h1>
        <form action="login.php" method="POST">
            <label for="pseudo">Nom d'utilisateur</label>
            <input type="text" name="pseudo" id="pseudo" placeholder="17sx" required>
            
            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" placeholder="123password" required>

            <button type="submit">Se connecter</button>
        </form>

        <a href="register.php">Pas de compte ?</a>
    </div>
</div>

<?php
session_start();
require_once 'bdd.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['pseudo']) && !empty($_POST['password'])) {
        $pseudo = $_POST['pseudo'];
        $password = $_POST['password'];

        $stmt = $connexion->prepare("SELECT id, pseudo, password, admin FROM user WHERE pseudo = :pseudo");
        $stmt->bindParam(':pseudo', $pseudo);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['pseudo'] = $user['pseudo'];
            $_SESSION['admin'] = $user['admin'];

            header("Location: index.php");
            exit();
        } else {
            echo "<script>alert Mauvais identifiants</script>";
        }
    } else {
        $error = "remplir tout les champs";
    }
}
?>


</body>
</html>
