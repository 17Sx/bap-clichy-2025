<?php

session_start();

if (!isset($_POST["token"]) || $_POST["token"] != $_SESSION['csrf_article_add']) {
    die('<p>CSRF token is invalid</p>');
}

unset($_SESSION['csrf_article_add']);
// Verification des elements envoyé
if (isset($_POST['title']) && !empty($_POST['title'])) {
    $title = $_POST['title'];
} else {
    echo "<p>Le titre est obligatoire</p>";
    header("Location: ./publier.php?error=Le+titre+est+obligatoire");
    exit();
}

if (isset($_POST['content']) && !empty($_POST['content'])) {
    $content = $_POST['content'];
} else {
    echo "<p>Le contenu est obligatoire</p>";
    header("Location: ./publier.php?error=Le+contenu+est+obligatoire");
    exit();
}

if (isset($_POST['slug']) && !empty($_POST['slug'])) {
    $slug = $_POST['slug'];
} else {
    echo "<p>Le slug est obligatoire</p>";
    header("Location: ./publier.php?error=Le+slug+est+obligatoire");
    exit();
}

if (isset($title) && isset($content) && isset($slug)) {
    // Pas d'erreur
    require_once 'bdd.php';
    // Verifier le slug pas de caractere chelou ou d'espace
    $getArticle = $connexion->prepare('SELECT title, content FROM Article Where slug = :slug LIMIT 1');
    $getArticle->execute(params:[
        'slug'=> htmlspecialchars(string: $_POST['slug'])
    ]);

    if($getArticle->rowCount() <= 0){
        $sauvegarde = $connexion->prepare('INSERT INTO Article (title, content, img, slug) VALUES (:title, :content, :img, :slug)');
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE){
            $destination = 'img/';

            // Nom du fichier de destination
            $fileName = basename( $_POST['slug'] . $_FILES['image']['name']);
            $fileDestination = $destination . $fileName;

            // Déplace le fichier téléchargé vers le dossier de destination
            move_uploaded_file($_FILES['image']['tmp_name'], $fileDestination);
                
            $sauvegarde->execute([
                'title' => $title,
                'content' => $content,
                'img' => $fileDestination,
                'slug' => $slug
            ]);
        }else{
            $sauvegarde->execute([
                'title' => $title,
                'content' => $content,
                'img' => "none",
                'slug' => $slug
            ]);
        }


        if ($sauvegarde->rowCount() > 0) {
            echo "<p>Sauvegarde effectuée</p>";
            header("location:./index.php");
            
        } else {
            echo "<p>Une erreur est survenue</p>";
            header("Location: ./publier.php?error=Une+erreur+est+survenue");
            exit();
    }
    }
    else{
        echo "<p>Une erreur est survenue avec le slug</p>";
        // page initiale
        header("Location: ./publier.php?error=Le+slug+doit+etre+unique");
        exit();
    }

}
?>
