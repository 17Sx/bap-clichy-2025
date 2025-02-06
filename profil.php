<?php

session_start();

if(!isset($_SESSION['user_connected']) || empty($_SESSION['user_connected'])){
  header("Location: ./SignIN_UP.php");
  exit();
}
else{
    require_once('bdd.php');
    if (!isset($_SESSION["csrf_article_add"]) || empty( $_SESSION["csrf_article_add"])) {
            $_SESSION["csrf_article_add"] = bin2hex(string: random_bytes(32 ));
        }
    $getuser = $connexion->prepare(query: 'SELECT id, email, user, administrateur FROM users WHERE id = :id' );
    $getuser->execute(params:[
        'id'=> htmlspecialchars(string: $_SESSION['user_connected'])
    ]);
    $getuser = $getuser->fetch();

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./src/profil.css">
    <title>Compte de <?php echo $getuser["user"];?></title>
</head>
<body>
    <?php require_once('header.php');?>
    <div class="profil-container">
        <h1><?php echo $getuser["user"];?></h1>
        <p><?php echo $getuser["email"];?></p>
        <div class="button-container">
        <a href="./deconnection.php" ><button >Se deconnecter</button></a> 
        <?php if($getuser["administrateur"]){?>
            <form  action="publier.php" method="POST">
                <input type="hidden" name="token" value="<?= $_SESSION['csrf_article_add']; ?>">
                <button type="submit">Publier un article</button>
            </form>
            
        <?php }?>
        </div>

    </div>  
</body>
</html>