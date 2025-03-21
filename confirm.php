<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

require_once 'bdd.php';

if (!isset($conn)) {
    try {
        $dsn = 'mysql:host=localhost;dbname=renduphpcrud;charset=utf8';
        $username = 'root';
        $password = '';
        $conn = new PDO($dsn, $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die('Erreur de connexion : ' . $e->getMessage());
    }
}

$messageId = htmlspecialchars($_GET['id']);
$userId = $_SESSION['user_id'];
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] == 1;

try {
    $stmt = $conn->prepare("
        SELECT m.*, u.pseudo, u.email 
        FROM message m
        JOIN user u ON m.user_id = u.id
        WHERE m.id = ?
    ");
    $stmt->execute([$messageId]);
    $messageInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$messageInfo) {
        header("Location: index.php");
        exit();
    }

    $tags = !empty($messageInfo['tags']) ? json_decode($messageInfo['tags'], true) : [];
    $isOwner = $messageInfo['user_id'] == $userId;
} catch (PDOException $e) {
    header("Location: index.php?error=database");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de commande - FoodShare</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/confirm.css">
    <link rel="stylesheet" href="css/var.css">
</head>

<body>
    <?php include 'templates/header.php'; ?>
    <div class="confirm-container">
        <div class="confirmation-page">
            <div class="confirmation-container">
                <?php if ($isAdmin): ?>
                    <div class="admin-notice">En tant qu'administrateur, vous ne pouvez pas réclamer d'annonces.</div>
                <?php endif; ?>

                <div class="order-summary">
                    <div class="order-detail">
                        <strong>Titre :</strong>
                        <span><?php echo htmlspecialchars($messageInfo['titre']); ?></span>
                    </div>

                    <div class="order-detail">
                        <strong>Ingrédients :</strong>
                        <span><?php echo htmlspecialchars($messageInfo['ingredients']); ?></span>
                    </div>

                    <?php if (!empty($tags)): ?>
                        <div class="order-detail">
                            <strong>Tags :</strong>
                            <div class="tag-container">
                                <?php
                                foreach ($tags as $tag) {
                                    $tag = trim($tag);
                                    $tagLower = strtolower(str_replace(
                                        [' ', 'é', 'è', 'ê', 'à', 'â', 'ô', 'ù', 'û', 'ç', 'î', 'ï'],
                                        ['-', 'e', 'e', 'e', 'a', 'a', 'o', 'u', 'u', 'c', 'i', 'i'],
                                        $tag
                                    ));
                                    $tagFile = "public/icon/{$tagLower}.png";

                                    echo '<div class="tag-item">';
                                    echo htmlspecialchars($tag);
                                    if (file_exists($tagFile)) {
                                        echo "<img src='{$tagFile}' alt='{$tag}' class='tag-icon'>";
                                    } else {
                                        echo "<p>Image non disponible</p>";
                                    }
                                    echo '</div>';
                                }
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($messageInfo['quantite'])): ?>
                        <div class="order-detail">
                            <strong>Quantité :</strong>
                            <span><?php echo htmlspecialchars($messageInfo['quantite']); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($messageInfo['lieu'])): ?>
                        <div class="order-detail">
                            <strong>Lieu de récupération :</strong>
                            <span><?php echo htmlspecialchars($messageInfo['lieu']); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($messageInfo['nom_adresse'])): ?>
                        <div class="order-detail">
                            <strong>Adresse :</strong>
                            <span><?php echo htmlspecialchars($messageInfo['nom_adresse']); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($messageInfo['date_peremption'])): ?>
                        <div class="order-detail">
                            <strong>Date de péremption :</strong>
                            <span><?php echo (new DateTime($messageInfo['date_peremption']))->format('d/m/Y'); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="order-detail">
                        <strong>Proposé par :</strong>
                        <span><?php echo htmlspecialchars($messageInfo['pseudo']); ?></span>
                    </div>

                    <div class="order-detail">
                        <strong>Contact :</strong>
                        <span><?php echo htmlspecialchars($messageInfo['email']); ?></span>
                    </div>

                    <div class="buttons-container">
                        <?php if ($isOwner): ?>
                            <a href="edit.php?id=<?php echo htmlspecialchars($messageId); ?>" class="btn-edit">Modifier l'annonce</a>
                            <form action="delete.php" method="GET" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette annonce ?');">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($messageId); ?>">
                                <button type="submit" class="btn-delete">Supprimer l'annonce</button>
                            </form>
                        <?php elseif (!$isAdmin): ?>
                            <form action="claim.php" method="POST">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($messageId); ?>">
                                <button type="submit" name="confirm" class="btn-confirm">Confirmer la commande</button>
                            </form>
                        <?php endif; ?>
                        <a href="index.php" class="btn-cancel">Annuler</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'templates/footer.php'; ?>
</body>

</html>