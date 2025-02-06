<?php
session_start();
// Si on n'a pas de csrf token, on en crée un
if(!isset($_SESSION['user_connected']) || empty($_SESSION['user_connected'])){
  header("Location: ./SignIN_UP.php");
  exit();
}

require_once('bdd.php');
$getuser = $connexion->prepare(query: 'SELECT id, email, user, administrateur FROM users WHERE id = :id' );
$getuser->execute(params:[
    'id'=> htmlspecialchars(string: $_SESSION['user_connected'])
]);
$getuser = $getuser->fetch();

if(!$getuser["administrateur"]){
    header("Location: ./SignIN_UP.php");
    exit();
}

$SupprimerArticle = $connexion->prepare('DELETE FROM Article Where slug = :slug');
$SupprimerArticle->execute(params:[
    'slug'=> htmlspecialchars(string: $_GET['s'])
]);

header("Location: ./index.php");
exit();
?>