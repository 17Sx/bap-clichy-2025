<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clichy | AntiGaspi - Filtrer par tags</title>
    <link rel="stylesheet" href="css/annonces.css">
</head>
<body>

<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$pseudo = $_SESSION['pseudo'];
$userId = $_SESSION['user_id'];
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] == 1;

$dsn = 'mysql:host=localhost;dbname=renduphpcrud;charset=utf8';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $allTags = [
        'Vegétarien', 'Végan', 'Pescetarien' , 'Sans gluten', 
'Avec viande' ,'Désserts'  ];

    $selectedTags = isset($_GET['tags']) ? explode(',', $_GET['tags']) : [];
    $selectedLieu = isset($_GET['lieu']) ? $_GET['lieu'] : '';
    $sortDate = isset($_GET['sort_date']) ? $_GET['sort_date'] : '';
    
    $lieuStmt = $pdo->query("SELECT DISTINCT lieu FROM message WHERE lieu IS NOT NULL ORDER BY lieu");
    $lieux = $lieuStmt->fetchAll(PDO::FETCH_COLUMN);
    
?>

<?php include 'templates/header.php'; ?>
<?php include 'templates/adminmessage.php'; ?>
<?php include 'templates/clientmessage.php'; ?>

<div class="container">
    <nav class="nav-header">
        <a href="index.php" class="home-btn">Accueil</a>
        <a href="logout.php" class="logout-btn">Déconnexion</a>
    </nav>

    <div class="filter-section">
        <h2>Filtrer les annonces</h2>
        
        <!-- Filtres supplémentaires: lieu et date de péremption -->
        <div class="filter-controls">
            <div class="filter-control">
                <label for="lieu">Filtrer par lieu:</label>
                <select id="lieu" name="lieu" onchange="applyFilters()">
                    <option value="">Tous les lieux</option>
                    <?php foreach ($lieux as $lieu): ?>
                        <option value="<?php echo htmlspecialchars($lieu); ?>" 
                                <?php echo $selectedLieu === $lieu ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($lieu); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-control">
                <label for="sort_date">Trier par date de péremption:</label>
                <select id="sort_date" name="sort_date" onchange="applyFilters()">
                    <option value="">Sans tri</option>
                    <option value="asc" <?php echo $sortDate === 'asc' ? 'selected' : ''; ?>>
                        Du plus récent au plus ancien
                    </option>
                    <option value="desc" <?php echo $sortDate === 'desc' ? 'selected' : ''; ?>>
                        Du plus ancien au plus récent
                    </option>
                </select>
            </div>
        </div>
        
        <div class="filter-form">
            <a href="javascript:void(0)" 
               onclick="selectTag('all')" 
               class="tag-button <?php echo empty($selectedTags) ? 'active' : ''; ?>">
                <img src="public/icons/all.svg" alt="Tous">
                <span>Tous</span>
            </a>
            <?php foreach ($allTags as $tag): ?>
            <a href="javascript:void(0)" 
               onclick="selectTag('<?php echo $tag; ?>')" 
               class="tag-button <?php echo in_array($tag, $selectedTags) ? 'active' : ''; ?>">
                <img src="public/icons/<?php echo strtolower(str_replace(' ', '-', $tag)); ?>.svg" alt="<?php echo $tag; ?>">
                <span><?php echo htmlspecialchars($tag); ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="messages-section">
        
        <div class="message-grid">
            <?php
            // Construire la requête SQL en fonction des filtres sélectionnés
            $sql = "SELECT user.pseudo, message.*, message.id AS message_id, message.user_id
                    FROM message
                    JOIN user ON message.user_id = user.id
                    WHERE 1=1";
            
            $params = [];
            
            if (!empty($selectedTags)) {
                $tagConditions = [];
                foreach ($selectedTags as $index => $tag) {
                    $paramName = ':tag' . $index;
                    $tagConditions[] = "JSON_CONTAINS(tags, $paramName, '$')";
                    $params[$paramName] = json_encode($tag);
                }
                $sql .= " AND (" . implode(' AND ', $tagConditions) . ")";
            }
            
            if (!empty($selectedLieu)) {
                $sql .= " AND lieu = :lieu";
                $params[':lieu'] = $selectedLieu;
            }
            
            // Ordre de tri
            if (!empty($sortDate)) {
                $sql .= " ORDER BY date_peremption " . ($sortDate === 'asc' ? 'ASC' : 'DESC');
            } else {
                $sql .= " ORDER BY message.creea DESC";
            }
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            $messageCount = 0;
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $messageCount++;
                $isOwnMessage = $row['user_id'] == $userId;
                $isClaimed = $row['is_claim'] == 1;
                $messageClass = $isClaimed ? 'claimed' : '';
                
                // Convertir les tags JSON en tableau PHP
                $tags = json_decode($row['tags'], true) ?? [];
                
                echo '<div class="message-card ' . $messageClass . '">';
                
                // Titre de l'annonce
                echo '<div class="card-title"><h3>' . htmlspecialchars($row['titre'] ?? 'Sans titre') . '</h3></div>';
                
                // Afficher le statut (disponible/épuisé)
                if ($isClaimed){
                    echo '<div class="status-container">';
                }else {
                    echo '<div class="status-container-available">';
                }
                if ($isClaimed) {
                    echo '<span class="status-text status-unavailable">Épuisé</span>';
                } else {
                    echo '<span class="status-text status-available">Disponible</span>';
                }
                echo '</div>';
                
                
                echo '</div>';
            }
            
            if ($messageCount === 0) {
                echo '<p>Aucune annonce ne correspond aux critères sélectionnés.</p>';
            }
            
            ?>
        </div>
    </div>
</div>

<script>
let selectedTags = <?php echo json_encode($selectedTags); ?>;

function selectTag(tag) {
    if (tag === 'all') {
        selectedTags = [];
        // Mettre à jour les classes des boutons
        document.querySelectorAll('.tag-button').forEach(button => {
            button.classList.remove('active');
        });
        document.querySelector('.tag-button[onclick="selectTag(\'all\')"]').classList.add('active');
    } else {
        const index = selectedTags.indexOf(tag);
        if (index === -1) {
            selectedTags.push(tag);
        } else {
            selectedTags.splice(index, 1);
        }
        
        // Mettre à jour les classes des boutons
        document.querySelectorAll('.tag-button').forEach(button => {
            if (button.getAttribute('onclick').includes(tag)) {
                button.classList.toggle('active');
            }
        });
        
        // Gérer le bouton "Tous"
        const allButton = document.querySelector('.tag-button[onclick="selectTag(\'all\')"]');
        if (selectedTags.length === 0) {
            allButton.classList.add('active');
        } else {
            allButton.classList.remove('active');
        }
    }

    applyFilters();
}

function applyFilters() {
    const lieu = document.getElementById('lieu').value;
    const sortDate = document.getElementById('sort_date').value;
    
    let params = new URLSearchParams();
    
    if (selectedTags.length > 0) {
        params.set('tags', selectedTags.join(','));
    }
    
    if (lieu) {
        params.set('lieu', lieu);
    }
    
    if (sortDate) {
        params.set('sort_date', sortDate);
    }
    
    const newUrl = `${window.location.pathname}?${params.toString()}`;
    window.history.pushState({}, '', newUrl);
    
    fetch(newUrl)
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Mettre à jour la grille des messages
            document.querySelector('.message-grid').innerHTML = 
                doc.querySelector('.message-grid').innerHTML;
            
            // Mettre à jour le titre
            document.querySelector('.messages-section h2').innerHTML = 
                doc.querySelector('.messages-section h2').innerHTML;
            
            // Mettre à jour les classes des boutons de tags
            const newButtons = doc.querySelectorAll('.tag-button');
            const currentButtons = document.querySelectorAll('.tag-button');
            
            newButtons.forEach((newButton, index) => {
                if (newButton.classList.contains('active')) {
                    currentButtons[index].classList.add('active');
                } else {
                    currentButtons[index].classList.remove('active');
                }
            });
        });
}

window.addEventListener('popstate', () => {
    location.reload();
});
</script>

<?php
} catch (PDOException $e) {
    echo 'Erreur : ' . $e->getMessage();
}
?>

<?php include 'templates/footer.php'; ?>

</body>
</html>