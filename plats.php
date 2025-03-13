<!DOCTYPE html>
<html>
<head>
    <title>Nos Plats</title>
    <style>
        .plat {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px;
        }
        form {
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <h1>Nos Plats</h1>
    
    <!-- Formulaire de filtrage -->
    <form method="GET" action="">
        <select name="filtre">
            <option value="">Tous les plats</option>
            <option value="vegan">Vegan</option>
            <option value="halal">Halal</option>
            <option value="sans_gluten">Sans gluten</option>
        </select>
        <button type="submit">Filtrer</button>
    </form>

    <?php
    // Votre code PHP ici (celui que je vous ai donné précédemment)
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=localhost;dbname=RenduPhpCRUD", "root", "");

// Récupération du filtre
$filtre = isset($_GET['filtre']) ? $_GET['filtre'] : '';

// Préparation de la requête SQL de base
$sql = "SELECT * FROM plats";

// Ajout des conditions selon le filtre
switch($filtre) {
    case 'vegan':
        $sql .= " WHERE is_vegan = 1";
        break;
    case 'halal':
        $sql .= " WHERE is_halal = 1";
        break;
    case 'sans_gluten':
        $sql .= " WHERE is_sans_gluten = 1";
        break;
    default:
        // Pas de filtre, on affiche tout
        break;
}

// Exécution de la requête
$stmt = $pdo->prepare($sql);
$stmt->execute();
$plats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Affichage des plats
foreach($plats as $plat) {
    echo "<div class='plat'>";
    echo "<h3>" . htmlspecialchars($plat['nom']) . "</h3>";
    echo "<p>" . htmlspecialchars($plat['description']) . "</p>";
    echo "<p>Prix : " . htmlspecialchars($plat['prix']) . " €</p>";
    echo "</div>";
}
?>

    <script>
        document.querySelector('select[name="filtre"]').addEventListener('change', function() {
            this.form.submit();
        });
    </script>
</body>
</html>