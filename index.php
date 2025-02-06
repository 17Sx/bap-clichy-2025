<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./src/feed.css">
    <link rel="stylesheet" href="./src/post.css">

    <title>Publication</title>
</head>
<body>

<?php
require_once('header.php');
?>
<div class="post-container">
<?php
echo '<h1>Articles publiés</h1>';
session_start();
require_once('bdd.php');
if (isset($_SESSION['user_connected'])){
    $getuser = $connexion->prepare(query: 'SELECT id, email, user, administrateur FROM users WHERE id = :id' );
    $getuser->execute(params:[
        'id'=> htmlspecialchars(string: $_SESSION['user_connected'])
    ]);
    $getuser = $getuser->fetch();
}
$getArticle = $connexion->prepare('SELECT title, content, img, slug FROM Article');
$getArticle->execute();


if($getArticle->rowCount() > 0){
foreach ($getArticle->fetchAll(PDO::FETCH_ASSOC) as $article){
    $image = $article['img'];
    $lien = "article.php?s=".$article['slug'];
    if($image!='none'){
        echo "<div class='post-photo'>";
        if($getuser["administrateur"]){
        echo '<a href="./Supprimer.php?s=' . $article['slug'] . '"><i class=\'fa-solid fa-trash\'></i></a>';
        echo '<a href="./modifier.php?s=' . $article['slug'] . '"><i class=\'fa-solid fa-pen\'></i></a>';
        }
        echo "<a href='$lien'>";
        echo "<img src='$image' alt='Image du post'>";
        echo "<div class='post-photo-slider'>";
        echo '<h1>'.$article['title'].'</h1>';
        echo '<p>'.$article['content'].'</p>';
        echo "</div>";
        echo "</a>";
        echo "</div>";
    }
    else{
        echo "<div class='post'>";
        if($getuser["administrateur"]){
        echo '<a href="./Supprimer.php?s=' . $article['slug'] . '"><i class=\'fa-solid fa-trash\'></i></a>';
        echo '<a href="./modifier.php?s=' . $article['slug'] . '"><i class=\'fa-solid fa-pen\'></i></a>';
        }
        echo "<a href='$lien'>";
        echo '<h1>'.$article['title'].'</h1>';
        echo '<hr>';
        echo '<p>'.$article['content'].'</p>';
        echo "</a>";
        echo "</div>";
    }

    
}}
else{
    echo '<h1>Aucun article publié</h1>';
}
?>
</div>

</body>
</html>