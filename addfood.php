<?php
session_start();

require_once 'config/config.php';

$pseudo = isset($_SESSION['pseudo']) ? $_SESSION['pseudo'] : '';

$isAdmin = (isset($_SESSION['admin']) && $_SESSION['admin'] == 1) || (isset($_SESSION['is_superadmin']) && $_SESSION['is_superadmin'] == 1);

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$locations = [
    'Lycée de Paris' => ['lat' => 48.8566, 'lng' => 2.3522],
    'Lycée de Boulogne' => ['lat' => 48.8333, 'lng' => 2.25],
    'Primaire de Garches' => ['lat' => 48.8461, 'lng' => 2.1882]
];

$apiKey = GOOGLE_MAPS_API_KEY;
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une annonce anti-gaspi</title>
    <link rel="stylesheet" href="css/global.css">
    <style>
        #map {
            height: 300px;
            width: 100%;
            margin-top: 10px;
            border-radius: 5px;
            display: none;
        }

        .map-container {
            margin-top: 10px;
        }
    </style>
</head>

<?php include 'templates/header.php'; ?>

<body>
    <?php if ($isAdmin): ?>

        <a class="back-btn" href="index.php">Retour</a>


        <div class="admin-form-container">
            <div class="message-form">
                <form action="create.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <div class="form-group">
                        <label for="titre">Titre de l'annonce:</label>
                        <input type="text" name="titre" id="titre" placeholder="Titre de votre annonce" required>
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
                        <label for="date_peremption">Date de péremption:</label>
                        <input type="date" name="date_peremption" id="date_peremption" required>
                    </div>

                    <div class="form-group">
                        <label for="tags">Tags (sélectionnez plusieurs options avec Ctrl+clic ou Cmd+clic):</label>
                        <select name="tags[]" id="tags" multiple class="form-control">
                            <option value="Pescetarien">🐟 Pescetarien</option>
                            <option value="Vegan">🌱 Vegan</option>
                            <option value="Sans Gluten">🌾 Sans Gluten</option>
                            <option value="Avec viande">🥩 Avec viande</option>
                            <option value="Végétarien">🥗 Végétarien</option>
                            <option value="Desserts">🍰 Desserts</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="lieu">Lieu de collecte:</label>
                        <select name="lieu" id="lieu" required>
                            <option value="">Sélectionnez un lieu</option>
                            <option value="Lycée de Paris">Lycée de Paris</option>
                            <option value="Lycée de Boulogne">Lycée de Boulogne</option>
                            <option value="Primaire de Garches">Primaire de Garches</option>
                        </select>
                    </div>

                    <div class="map-container">
                        <div id="map"></div>
                    </div>

                    <div class="btn-container">
                        <button type="submit">Envoyer l'annonce</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            const locations = <?php echo json_encode($locations); ?>;
            let map;
            let marker;

            function initMap() {
                const defaultLocation = {
                    lat: 46.603354,
                    lng: 1.888334
                };
                map = new google.maps.Map(document.getElementById("map"), {
                    zoom: 12,
                    center: defaultLocation,
                });

                marker = new google.maps.Marker({
                    position: defaultLocation,
                    map: map,
                    visible: false
                });

                document.getElementById('lieu').addEventListener('change', function() {
                    const selectedLocation = this.value;

                    if (selectedLocation && locations[selectedLocation]) {
                        document.getElementById('map').style.display = 'block';

                        const position = {
                            lat: locations[selectedLocation].lat,
                            lng: locations[selectedLocation].lng
                        };

                        map.setCenter(position);

                        marker.setPosition(position);
                        marker.setVisible(true);
                    } else {
                        document.getElementById('map').style.display = 'none';
                        marker.setVisible(false);
                    }
                });
            }
        </script>

        <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAPS_API_KEY; ?>&callback=initMap">
        </script>

    <?php else: ?>
        <p>Vous devez être administrateur pour accéder à cette page.</p>
    <?php endif; ?>

    <?php include 'templates/footer.php'; ?>
</body>

</html>