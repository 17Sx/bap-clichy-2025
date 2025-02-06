<?php
session_start();
// Si on n'a pas de csrf token, on en crée un
if(
  !isset($_SESSION['csrf_article_add']) || 
  empty($_SESSION['csrf_article_add'])
){
  $_SESSION['csrf_article_add'] = bin2hex(random_bytes(32));
}

if (isset($_GET['error'])) {
  echo "<h3>" . htmlspecialchars($_GET['error']) . "</h3>";
}

if(
  !isset($_POST['token']) || 
  $_POST['token'] != $_SESSION['csrf_article_add']
){
  header("Location: ./profil.php");
  die('<p>CSRF invalide</p>');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./src/publier.css">
    <title>Document</title>
</head>
<body>
  <?php require_once('header.php');?>
    <form action="traitement.php" method="Post" enctype="multipart/form-data">
        <label for="image">Image :</label>
        <input type="file" name="image" id="image" accept="image/*" >
        <br>
        <label for="title">Titre :</label>
        <input type="text" name="title" id="title" placeholder="Article 1" >
        <br>
        <label for="content">Contenu :</label>
        <textarea type="text" name="content" id="content" rows="10" colums="30"></textarea>
        <br>
        <label for="slug">Slug :</label>
        <input type="text" name="slug" id="slug" placeholder="article-1" >
        <br>
        <input type="hidden" name="token" value="<?= $_SESSION["csrf_article_add"]?>">
        <input type="submit" class="button" name="ajouter" value="Ajouter">   
    </form>
    
</body>
</html>