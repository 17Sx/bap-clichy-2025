<?php
session_start();

// Vérification que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$dsn = 'mysql:host=localhost;dbname=renduphpcrud;charset=utf8';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérification que l'ID de l'annonce est fourni
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        header("Location: index.php");
        exit();
    }

    $message_id = $_GET['id'];

    // Récupération de l'annonce et de son auteur
    $stmt = $pdo->prepare("
        SELECT *
        FROM message
        WHERE id = :id
    ");
    $stmt->execute([':id' => $message_id]);
    $message = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$message) {
        header("Location: index.php");
        exit();
    }

    // Vérifier si l'utilisateur est l'auteur de l'annonce
    if ($message['user_id'] != $_SESSION['user_id']) {
        // Rediriger vers la page d'accueil si l'utilisateur n'est pas l'auteur
        header("Location: index.php");
        exit();
    }

    // Traitement du formulaire de modification
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!empty($_POST['id'])) {
            $message_id = $_POST['id'];

            $titre = $_POST['titre'] ?? '';
            $content = $_POST['content'] ?? '';
            $ingredients = $_POST['ingredients'] ?? '';
            $quantite = $_POST['quantite'] ?? '';
            $nom_adresse = $_POST['nom_adresse'] ?? '';
            $lieu = $_POST['lieu'] ?? '';
            $date_peremption = $_POST['date_peremption'] ?? '';

            $tags = isset($_POST['tags']) ? json_encode($_POST['tags']) : '';

            $image_path = null;
            if (!empty($_FILES['image']['name'])) {
                $target_dir = "uploads/";

                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                $target_file = $target_dir . basename($_FILES["image"]["name"]);
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                $check = getimagesize($_FILES["image"]["tmp_name"]);
                if ($check !== false) {
                    $new_filename = uniqid() . '.' . $imageFileType;
                    $target_file = $target_dir . $new_filename;

                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                        $image_path = $target_file;
                    }
                }
            }

            $sql = "UPDATE message SET 
                    titre = :titre, 
                    content = :content, 
                    ingredients = :ingredients, 
                    quantite = :quantite, 
                    nom_adresse = :nom_adresse,
                    lieu = :lieu,
                    date_peremption = :date_peremption,
                    tags = :tags
                    WHERE id = :id AND user_id = :user_id";

            if ($image_path) {
                $sql = "UPDATE message SET 
                        titre = :titre, 
                        content = :content, 
                        ingredients = :ingredients, 
                        quantite = :quantite, 
                        nom_adresse = :nom_adresse,
                        lieu = :lieu,
                        date_peremption = :date_peremption,
                        tags = :tags,
                        image_path = :image_path
                        WHERE id = :id AND user_id = :user_id";
            }

            $stmt = $pdo->prepare($sql);

            // Limiter le contenu à 255 caractères pour correspondre à la structure de la BDD
            $content = substr($content, 0, 255);

            $params = [
                ':titre' => $titre,
                ':content' => $content,
                ':ingredients' => $ingredients,
                ':quantite' => $quantite,
                ':nom_adresse' => $nom_adresse,
                ':lieu' => $lieu,
                ':date_peremption' => $date_peremption,
                ':tags' => $tags,
                ':id' => $message_id,
                ':user_id' => $_SESSION['user_id']
            ];

            if ($image_path) {
                $params[':image_path'] = $image_path;
            }

            try {
                if ($stmt->execute($params)) {
                    header("Location: index.php");
                    exit();
                } else {
                    $error = "Erreur lors de la mise à jour";
                }
            } catch (PDOException $e) {
                $error = "Erreur SQL : " . $e->getMessage();
            }
        }
    }
} catch (PDOException $e) {
    echo 'Erreur : ' . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une annonce - Clichy AntiGaspi</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/var.css">
    <style>
        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 2rem;
            text-align: center;
            font-size: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #34495e;
            font-weight: 600;
        }

        input[type="text"],
        input[type="date"],
        textarea,
        select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        textarea:focus,
        select:focus {
            border-color: var(--blue);
            outline: none;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .current-image {
            margin: 1rem 0;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .current-image img {
            max-width: 200px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn-primary,
        .btn-secondary {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--blue);
            color: white;
        }

        .btn-primary:hover {
            background: var(--blue);
        }

        .btn-secondary {
            background: #95a5a6;
            color: white;
            text-decoration: none;
        }

        .btn-secondary:hover {
            background: #7f8c8d;
        }

        select[multiple] {
            height: 150px;
        }

        small {
            display: block;
            margin-top: 0.5rem;
            color: #7f8c8d;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Modifier une annonce</h1>

        <form action="edit.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($message['id']); ?>">

            <div class="form-group">
                <label for="titre">Titre de l'annonce:</label>
                <input type="text" name="titre" id="titre" value="<?php echo htmlspecialchars($message['titre'] ?? ''); ?>" placeholder="Titre de votre annonce" required>
            </div>

            <div class="form-group">
                <label for="content">Description:</label>
                <textarea name="content" id="content" placeholder="Description de l'annonce" required><?php echo htmlspecialchars($message['content'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="ingredients">Ingrédients:</label>
                <textarea name="ingredients" id="ingredients" placeholder="Listez les ingrédients, un par ligne"><?php echo htmlspecialchars($message['ingredients'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="quantite">Quantité:</label>
                <input type="text" name="quantite" id="quantite" value="<?php echo htmlspecialchars($message['quantite'] ?? ''); ?>" placeholder="Ex: 500g, 2 portions, etc.">
            </div>

            <div class="form-group">
                <label for="nom_adresse">Nom et adresse:</label>
                <input type="text" name="nom_adresse" id="nom_adresse" value="<?php echo htmlspecialchars($message['nom_adresse'] ?? ''); ?>" placeholder="Votre nom et adresse">
            </div>

            <div class="form-group">
                <label for="lieu">Lieu de collecte:</label>
                <select name="lieu" id="lieu" required>
                    <option value="Lycée de Paris" <?php echo ($message['lieu'] ?? '') == 'Lycée de Paris' ? 'selected' : ''; ?>>Lycée de Paris</option>
                    <option value="Lycée de Boulogne" <?php echo ($message['lieu'] ?? '') == 'Lycée de Boulogne' ? 'selected' : ''; ?>>Lycée de Boulogne</option>
                    <option value="Primaire de Garches" <?php echo ($message['lieu'] ?? '') == 'Primaire de Garches' ? 'selected' : ''; ?>>Primaire de Garches</option>
                </select>
            </div>

            <div class="form-group">
                <label for="date_peremption">Date de péremption:</label>
                <input type="date" name="date_peremption" id="date_peremption" value="<?php echo htmlspecialchars($message['date_peremption'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="tags">Tags (sélectionnez plusieurs options avec Ctrl+clic ou Cmd+clic):</label>
                <?php
                $selectedTags = !empty($message['tags']) ? json_decode($message['tags'], true) : [];
                if (!is_array($selectedTags)) $selectedTags = [];
                ?>
                <select name="tags[]" id="tags" multiple class="form-control">
                    <option value="pescetarien" <?php echo in_array('pescetarien', $selectedTags) ? 'selected' : ''; ?>>🐟 Pescetarien</option>
                    <option value="vegan" <?php echo in_array('vegan', $selectedTags) ? 'selected' : ''; ?>>🌱 Vegan</option>
                    <option value="végétarien" <?php echo in_array('végétarien', $selectedTags) ? 'selected' : ''; ?>>🥗 Végétarien</option>
                    <option value="desserts" <?php echo in_array('desserts', $selectedTags) ? 'selected' : ''; ?>>🍰 Desserts</option>
                    <option value="sans gluten" <?php echo in_array('sans gluten', $selectedTags) ? 'selected' : ''; ?>>🌾 Sans gluten</option>
                    <option value="avec viande" <?php echo in_array('avec viande', $selectedTags) ? 'selected' : ''; ?>>🥩 Avec viande</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Enregistrer les modifications</button>
                <a href="index.php" class="btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</body>

</html>