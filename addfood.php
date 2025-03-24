<?php
session_start();

require_once 'config/config.php';

$pseudo = isset($_SESSION['pseudo']) ? $_SESSION['pseudo'] : '';

$isAdmin = (isset($_SESSION['admin']) && $_SESSION['admin'] == 1) || (isset($_SESSION['is_superadmin']) && $_SESSION['is_superadmin'] == 1);

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$locations = [
    // Écoles élémentaires
    'École élémentaire publique Jean Jaurès' => ['lat' => 48.9022, 'lng' => 2.3094],
    'École élémentaire publique Jules Ferry A' => ['lat' => 48.9022, 'lng' => 2.3094],
    'École élémentaire publique Jules Ferry B' => ['lat' => 48.9022, 'lng' => 2.3094],
    'École élémentaire publique Léopold Sédar Senghor' => ['lat' => 48.9022, 'lng' => 2.3094],
    'École élémentaire publique Louis Aragon' => ['lat' => 48.9022, 'lng' => 2.3094],
    'École élémentaire publique Louis Pasteur A' => ['lat' => 48.9022, 'lng' => 2.3094],
    'École élémentaire publique Louis Pasteur B' => ['lat' => 48.9022, 'lng' => 2.3094],
    'École élémentaire publique Toussaint Louverture' => ['lat' => 48.9022, 'lng' => 2.3094],
    'École élémentaire publique Victor Hugo A' => ['lat' => 48.9022, 'lng' => 2.3094],
    'École élémentaire publique Victor Hugo B' => ['lat' => 48.9022, 'lng' => 2.3094],

    // Écoles maternelles
    'École maternelle publique du Landy' => ['lat' => 48.9022, 'lng' => 2.3094],
    'École maternelle publique Georges Boisseau' => ['lat' => 48.9022, 'lng' => 2.3094],
    'École maternelle publique Jacques Prévert' => ['lat' => 48.9022, 'lng' => 2.3094],
    'École maternelle publique Jean Jaurès' => ['lat' => 48.9022, 'lng' => 2.3094],
    'École maternelle publique Jules Ferry' => ['lat' => 48.9022, 'lng' => 2.3094],
    'École maternelle publique Louis Pasteur' => ['lat' => 48.9022, 'lng' => 2.3094],
    'École maternelle publique Marin Fournier' => ['lat' => 48.9022, 'lng' => 2.3094],
    'École maternelle publique Pierre Mendès-France' => ['lat' => 48.9022, 'lng' => 2.3094],
    'École maternelle publique Sophie Foucault' => ['lat' => 48.9022, 'lng' => 2.3094],
    'École maternelle publique Victor Hugo' => ['lat' => 48.9022, 'lng' => 2.3094],
    'École maternelle publique Annie Fratellini' => ['lat' => 48.9022, 'lng' => 2.3094],
    'École maternelle publique Condorcet' => ['lat' => 48.9022, 'lng' => 2.3094]
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
                            <option value="">Sélectionnez une école</option>

                            <optgroup label="Écoles élémentaires">
                                <option value="École élémentaire publique Jean Jaurès">École élémentaire publique Jean Jaurès</option>
                                <option value="École élémentaire publique Jules Ferry A">École élémentaire publique Jules Ferry A</option>
                                <option value="École élémentaire publique Jules Ferry B">École élémentaire publique Jules Ferry B</option>
                                <option value="École élémentaire publique Léopold Sédar Senghor">École élémentaire publique Léopold Sédar Senghor</option>
                                <option value="École élémentaire publique Louis Aragon">École élémentaire publique Louis Aragon</option>
                                <option value="École élémentaire publique Louis Pasteur A">École élémentaire publique Louis Pasteur A</option>
                                <option value="École élémentaire publique Louis Pasteur B">École élémentaire publique Louis Pasteur B</option>
                                <option value="École élémentaire publique Toussaint Louverture">École élémentaire publique Toussaint Louverture</option>
                                <option value="École élémentaire publique Victor Hugo A">École élémentaire publique Victor Hugo A</option>
                                <option value="École élémentaire publique Victor Hugo B">École élémentaire publique Victor Hugo B</option>
                            </optgroup>

                            <optgroup label="Écoles maternelles">
                                <option value="École maternelle publique du Landy">École maternelle publique du Landy</option>
                                <option value="École maternelle publique Georges Boisseau">École maternelle publique Georges Boisseau</option>
                                <option value="École maternelle publique Jacques Prévert">École maternelle publique Jacques Prévert</option>
                                <option value="École maternelle publique Jean Jaurès">École maternelle publique Jean Jaurès</option>
                                <option value="École maternelle publique Jules Ferry">École maternelle publique Jules Ferry</option>
                                <option value="École maternelle publique Louis Pasteur">École maternelle publique Louis Pasteur</option>
                                <option value="École maternelle publique Marin Fournier">École maternelle publique Marin Fournier</option>
                                <option value="École maternelle publique Pierre Mendès-France">École maternelle publique Pierre Mendès-France</option>
                                <option value="École maternelle publique Sophie Foucault">École maternelle publique Sophie Foucault</option>
                                <option value="École maternelle publique Victor Hugo">École maternelle publique Victor Hugo</option>
                                <option value="École maternelle publique Annie Fratellini">École maternelle publique Annie Fratellini</option>
                                <option value="École maternelle publique Condorcet">École maternelle publique Condorcet</option>
                            </optgroup>
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
                // Centrer la carte sur Clichy
                const clichyLocation = {
                    lat: 48.9022,
                    lng: 2.3094
                };

                map = new google.maps.Map(document.getElementById("map"), {
                    zoom: 14,
                    center: clichyLocation,
                    styles: [{
                        "featureType": "poi.school",
                        "elementType": "labels",
                        "stylers": [{
                            "visibility": "on"
                        }]
                    }]
                });

                marker = new google.maps.Marker({
                    position: clichyLocation,
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
                        map.setZoom(16);

                        marker.setPosition(position);
                        marker.setVisible(true);

                        // Ajouter une info window avec le nom de l'école
                        const infoWindow = new google.maps.InfoWindow({
                            content: selectedLocation
                        });

                        infoWindow.open(map, marker);
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