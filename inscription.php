<?php
require_once('bdd.php');

$inscription = $connexion->prepare('INSERT INTO `users`(`user`, `email`, `mdp`) VALUES (:user, :email, :mdp)');
$inscription->execute(params:[
    'user'=> htmlspecialchars(string: $_POST['user']),
    'email'=> htmlspecialchars(string: $_POST['email']),
    'mdp'=> htmlspecialchars(string: password_hash($_POST['password'],PASSWORD_BCRYPT,[]))
]);
header("Location: ./profil.php");
?>

