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
                if($check !== false) {
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
                    tags = :tags";
            
            if ($image_path) {
                $sql .= ", image_path = :image_path";
            }
            
            $sql .= " WHERE id = :id AND user_id = :user_id";
            
            $stmt = $pdo->prepare($sql);
            
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
            
            $stmt->execute($params);
            
            header("Location: index.php");
            exit();
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
    <link rel="stylesheet" href="css/global.css">
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
                    <option value="légumes" <?php echo in_array('légumes', $selectedTags) ? 'selected' : ''; ?>>Légumes</option>
                    <option value="fruits" <?php echo in_array('fruits', $selectedTags) ? 'selected' : ''; ?>>Fruits</option>
                    <option value="viande" <?php echo in_array('viande', $selectedTags) ? 'selected' : ''; ?>>Viande</option>
                    <option value="poisson" <?php echo in_array('poisson', $selectedTags) ? 'selected' : ''; ?>>Poisson</option>
                    <option value="produits laitiers" <?php echo in_array('produits laitiers', $selectedTags) ? 'selected' : ''; ?>>Produits laitiers</option>
                    <option value="bio" <?php echo in_array('bio', $selectedTags) ? 'selected' : ''; ?>>Bio</option>
                    <option value="gratuit" <?php echo in_array('gratuit', $selectedTags) ? 'selected' : ''; ?>>Gratuit</option>
                    <option value="à petit prix" <?php echo in_array('à petit prix', $selectedTags) ? 'selected' : ''; ?>>À petit prix</option>
                    <option value="fait maison" <?php echo in_array('fait maison', $selectedTags) ? 'selected' : ''; ?>>Fait maison</option>
                    <option value="végétarien" <?php echo in_array('végétarien', $selectedTags) ? 'selected' : ''; ?>>Végétarien</option>
                    <option value="vegan" <?php echo in_array('vegan', $selectedTags) ? 'selected' : ''; ?>>Vegan</option>
                    <option value="sans gluten" <?php echo in_array('sans gluten', $selectedTags) ? 'selected' : ''; ?>>Sans gluten</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="image">Image:</label>
                <?php if (!empty($message['image_path'])): ?>
                <div class="current-image">
                    <p>Image actuelle:</p>
                    <img src="<?php echo htmlspecialchars($message['image_path']); ?>" alt="Image actuelle" style="max-width: 200px;">
                </div>
                <?php endif; ?>
                <input type="file" name="image" id="image" accept="image/*">
                <small>Laissez vide pour conserver l'image actuelle</small>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary">Enregistrer les modifications</button>
                <a href="index.php" class="btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
    
    <footer>
        <div class="footer-left">
            <img src="public/img/logoblanc.png" alt="">
            <div class="footer-text">
                <p>Les restes d'aujourd'hui,</p>
                <p>les repas de demain</p>
            </div>
        </div>

        <div class="footer-contact">
            <h3>Contact</h3>
            <div class="footer-contact-text">
                <p>Mairie de Clichy-la-Garenne 80,</p>
                <p>Boulevard Jean Jaurès </p>
                <p>92110 Clichy </p>
                <p>01 47 15 30 00</p>
            </div>
        </div>
    </footer>
</body>
</html>