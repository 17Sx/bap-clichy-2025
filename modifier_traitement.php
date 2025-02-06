<?php
session_start(); 
if(
  !isset($_POST['token']) || 
  $_POST['token'] != $_SESSION['csrf_article_add']
){
  header("Location: ./SignIN_UP.php");
  exit();
}
require_once('bdd.php');
$ModifierArticle = $connexion->prepare('UPDATE `article` SET title = :title, content = :content, img = :img, slug = :slug WHERE slug = :oldslug');
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE){
            $destination = 'img/';

            // Nom du fichier de destination
            $fileName = basename( $_POST['slug'] . $_FILES['image']['name']);
            $fileDestination = $destination . $fileName;

            // Déplace le fichier téléchargé vers le dossier de destination
            move_uploaded_file($_FILES['image']['tmp_name'], $fileDestination);
                
            $ModifierArticle->execute([
                'title' => $_POST['title'],
                'content' => $_POST['content'],
                'img' => $fileDestination,
                'slug' => $_POST['slug'],
                'oldslug' => $_POST['s']
            ]);
        }else{
            $ModifierArticle->execute([
                'title' => $_POST['title'],
                'content' => $_POST['content'],
                'img' => "none",
                'slug' => $_POST['slug'],
                'oldslug' =>$_POST['s']
            ]);
        }
    header("Location: ./article.php?s=" . urlencode($_POST['slug']));
  exit();
?>