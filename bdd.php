<?php 

try {
    $connexion = new PDO("mysql:host=localhost;dbname=RenduPhpCRUD", 'root', '');
} catch (Exception $e) {
    die($e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['form']) && $_POST['form'] == 'ajout') {
    if (!empty($_POST['pseudo']) && !empty($_POST['email']) && !empty($_POST['password'])) {
        $pseudo = $_POST['pseudo'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $requete = $connexion->prepare("INSERT INTO user (pseudo, email, password) VALUES (:pseudo, :email, :password)");
        $requete->bindParam(':pseudo', $pseudo);
        $requete->bindParam(':email', $email);
        $requete->bindParam(':password', $password);

        if ($requete->execute()) {
            header("Location: login.php");
        } else {
            echo '<script type="text/javascript">alert("Votre compte n\'a pas pu être créé !");</script>';  
            header("Location: register.php");
        }
    } else {
        echo '<script type="text/javascript">alert("Veuillez remplir tous les champs SVP !");</script>';
        header("Location: register.php");
    }  
}



