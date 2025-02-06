<?php

session_start();


$email = $_POST['email'];
$mdp = $_POST['password'];

require_once('bdd.php');

$getpassword = $connexion->prepare(query: 'SELECT mdp FROM users WHERE email = :email' );
$getpassword->execute(params:[
    'email'=> htmlspecialchars(string: $email)
]);

$hashedPassword = $getpassword->fetch(PDO::FETCH_ASSOC);

if ($hashedPassword) {
    $hashedPassword = $hashedPassword['mdp'];
} else {
    header("Location: ./SignIN_UP.php?error=Mauvais+email");
    exit();
}

if (password_verify($mdp, $hashedPassword)){
    $getuser = $connexion->prepare(query: 'SELECT id, email, user, administrateur FROM users WHERE email = :email' );
    $getuser->execute(params:[
        'email'=> htmlspecialchars(string: $email)
    ]);
    $getuser = $getuser->fetch();
    session_unset();
    if ($getuser["administrateur"]) {
        if (!isset($_SESSION["csrf_article_add"]) || empty( $_SESSION["csrf_article_add"])) {
            $_SESSION["csrf_article_add"] = bin2hex(string: random_bytes(32 ));
        }
    }
    $_SESSION["user_connected"] = $getuser["id"];
    header("Location: ./profil.php");
    
}else{
    header("Location: ./SignIN_UP.php?error=Mauvais+mot+de+passe");
    exit();
}

?>