<?php
session_start();

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
if(
  !isset($_SESSION['csrf_article_add']) || 
  empty($_SESSION['csrf_article_add'])
){
  $_SESSION['csrf_article_add'] = bin2hex(random_bytes(32));
}
$getArticle = $connexion->prepare('SELECT title, content, img, slug FROM Article Where slug = :slug LIMIT 1');
$getArticle->execute(params:[
    'slug'=> htmlspecialchars(string: $_GET['s'])
]);
if($getArticle->rowCount()==1){
    $article = $getArticle->fetch();
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
    <form action="./modifier_traitement.php" method="POST" enctype="multipart/form-data">
        <label for="image">Image :</label>
        <input type="file" name="image" id="image" accept="image/*">
        <br>
        <label for="title">Titre :</label>
        <input type="text" name="title" id="title" placeholder="Article 1" value="<?php echo htmlspecialchars($article['title']); ?>">
        <br>
        <label for="content">Contenu :</label>
        <textarea name="content" id="content" rows="10" cols="30"><?php echo htmlspecialchars($article['content'], ENT_QUOTES); ?></textarea>
        <br>
        <label for="slug">Slug :</label>
        <input type="text" name="slug" id="slug" placeholder="article-1" value=<?php echo $article['slug'] ;?> >
        <br>
        <input type="hidden" name="token" value="<?= $_SESSION["csrf_article_add"]?>">
        <input type="hidden" name="s" value="<?= htmlspecialchars(string: $_GET['s'])?>">
        <input type="submit" class="button" name="modifier" value="Modifier"> 
    </form>
</body>
</html>