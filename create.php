<?php
session_start();

if (!isset($_SESSION['csrf_token']) || empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        echo "non non ";
        exit();
    }

    if (!empty($_POST['content'])) {
        $content = $_POST['content'];
        $titre = isset($_POST['titre']) ? $_POST['titre'] : null;
        $ingredients = isset($_POST['ingredients']) ? $_POST['ingredients'] : null;
        $quantite = isset($_POST['quantite']) ? $_POST['quantite'] : null;
        $nom_adresse = isset($_POST['nom_adresse']) ? $_POST['nom_adresse'] : null;
        
        // Traitement des tags depuis le select multiple
        $tags_array = [];
        if (isset($_POST['tags']) && is_array($_POST['tags'])) {
            $tags_array = $_POST['tags'];
        }
        $tags_json = json_encode($tags_array);
        
        $image_path = null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/'; 
            
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $new_filename = uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;

            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($file_extension, $allowed_types)) {
                die('Type de fichier non autorisé');
            }

            if ($_FILES['image']['size'] > 5000000) {
                die('Fichier trop volumineux');
            }

            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_path = $upload_path;
            } else {
                die('Erreur lors de l\'upload du fichier');
            }
        }

        require 'bdd.php';

        $stmt = $connexion->prepare("INSERT INTO message (user_id, content, image_path, titre, ingredients, tags, quantite, nom_adresse) 
                                    VALUES (:user_id, :content, :image_path, :titre, :ingredients, :tags, :quantite, :nom_adresse)");

        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':image_path', $image_path);
        $stmt->bindParam(':titre', $titre);
        $stmt->bindParam(':ingredients', $ingredients);
        $stmt->bindParam(':tags', $tags_json);
        $stmt->bindParam(':quantite', $quantite);
        $stmt->bindParam(':nom_adresse', $nom_adresse);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            echo "Erreur lors de l'ajout du message: " . implode(', ', $stmt->errorInfo());
        }
    }
}
?>

