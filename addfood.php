<?php
session_start();

$pseudo = isset($_SESSION['pseudo']) ? $_SESSION['pseudo'] : '';

$isAdmin = (isset($_SESSION['admin']) && $_SESSION['admin'] == 1) || (isset($_SESSION['is_superadmin']) && $_SESSION['is_superadmin'] == 1);

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une annonce anti-gaspi</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php if ($isAdmin): ?>
    <div class="message-form">
        <h2>Créer une nouvelle annonce anti gaspi!</h2>
        <form action="create.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="form-group">
                <label for="titre">Titre de l'annonce:</label>
                <input type="text" name="titre" id="titre" placeholder="Titre de votre annonce" required>
            </div>
            
            <div class="form-group">
                <label for="content">Description:</label>
                <textarea name="content" id="content" placeholder="Écrivez votre annonce ici, <?php echo htmlspecialchars($pseudo); ?>" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="ingredients">Ingrédients:</label>
                <textarea name="ingredients" id="ingredients" placeholder="Listez les ingrédients, un par ligne"></textarea>
            </div>
            
            <div class="form-group">
                <label for="quantite">Quantité:</label>
                <input type="text" name="quantite" id="quantite" placeholder="Ex: 500g, 2 portions, etc.">
            </div>
            
            <div class="form-group">
                <label for="nom_adresse">Nom et adresse:</label>
                <input type="text" name="nom_adresse" id="nom_adresse" placeholder="Votre nom et adresse">
            </div>
            
            <div class="form-group">
                <label for="lieu">Lieu de collecte:</label>
                <select name="lieu" id="lieu" required>
                    <option value="Lycée de Paris">Lycée de Paris</option>
                    <option value="Lycée de Boulogne">Lycée de Boulogne</option>
                    <option value="Primaire de Garches">Primaire de Garches</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="date_peremption">Date de péremption:</label>
                <input type="date" name="date_peremption" id="date_peremption" required>
            </div>
            
            <div class="form-group">
                <label for="tags">Tags (sélectionnez plusieurs options avec Ctrl+clic ou Cmd+clic):</label>
                <select name="tags[]" id="tags" multiple class="form-control">
                    <option value="légumes">Légumes</option>
                    <option value="fruits">Fruits</option>
                    <option value="viande">Viande</option>
                    <option value="poisson">Poisson</option>
                    <option value="produits laitiers">Produits laitiers</option>
                    <option value="bio">Bio</option>
                    <option value="gratuit">Gratuit</option>
                    <option value="à petit prix">À petit prix</option>
                    <option value="fait maison">Fait maison</option>
                    <option value="végétarien">Végétarien</option>
                    <option value="vegan">Vegan</option>
                    <option value="sans gluten">Sans gluten</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="image">Image:</label>
                <input type="file" name="image" id="image" accept="image/*">
            </div>
            
            <button type="submit">Envoyer l'annonce</button>
        </form>
    </div>
    <?php else: ?>
        <p>Vous devez être administrateur pour accéder à cette page.</p>
    <?php endif; ?>
</body>
</html>