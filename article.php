<?php
if (!isset($_GET["s"]) || empty($_GET['s'])){
    die('<p>Article introuvable</p>');
}
session_start();
require_once('bdd.php');
$getuser = $connexion->prepare(query: 'SELECT id, email, user, administrateur FROM users WHERE id = :id' );
$getuser->execute(params:[
    'id'=> htmlspecialchars(string: $_SESSION['user_connected'])
]);
$getuser = $getuser->fetch();
$getArticle = $connexion->prepare('SELECT title, content, img, slug FROM Article Where slug = :slug LIMIT 1');
$getArticle->execute(params:[
    'slug'=> htmlspecialchars(string: $_GET['s'])
]);
if($getArticle->rowCount()==1){
    $article = $getArticle->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./src/feed.css">
    <link rel="stylesheet" href="./src/post.css">
    
    <title><?php echo $article['title'];?></title>
</head>
<body>
    <?php 


    require_once('./header.php') ;
    ?>
    <div class="post-container">
<?php
    echo '<h1>Article : '.$article['title'].'</h1>';
    $image = $article['img'];
    if($image != 'none'){
        echo "<div class='post-photo'>";
        if($getuser["administrateur"]){
        echo '<a href="./Supprimer.php?s=' . $article['slug'] . '"><i class=\'fa-solid fa-trash\'></i></a>';
        echo '<a href="./modifier.php?s=' . $article['slug'] . '"><i class=\'fa-solid fa-pen\'></i></a>';
        }
        echo "<img src='$image' alt='Image du post'>";
        echo "<div class='post-photo-slider'>";
        echo '<h1>'.$article['title'].'</h1>';
        echo '<p>'.$article['content'].'</p>';
        echo "</div>";
        echo "</div>";
    }
    else{
        echo "<div class='post'>";
        if($getuser["administrateur"]){
        echo '<a href="./Supprimer.php?s=' . $article['slug'] . '"><i class=\'fa-solid fa-trash\'></i></a>';
        echo '<a href="./modifier.php?s=' . $article['slug'] . '"><i class=\'fa-solid fa-pen\'></i></a>';
        }
        echo '<h1>'.$article['title'].'</h1>';
        echo '<hr>';
        echo '<p>'.$article['content'].'</p>';
        echo "</div>";
    }
}
else{
    echo'<p>Article introuvable</p>';
}
?>
</div>
</body>
</html>