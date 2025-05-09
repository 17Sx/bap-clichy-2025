<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clichy | AntiGaspi</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body>

    <?php
    session_start();

    if (!isset($_SESSION['csrf_token']) || empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $pseudo = $_SESSION['pseudo'];
    $userId = $_SESSION['user_id'];
    $isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] == 1;
    $isSuperAdmin = isset($_SESSION['is_superadmin']) && $_SESSION['is_superadmin'] == 1;
    $isnotAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] == 0;
    ?>

    <?php include 'templates/header.php'; ?>
    <?php if ($isSuperAdmin): ?>
        <div class="admin-buttons">
            <a href="superadmin.php" class="admin-btn">Gestion des comptes</a>
            <a href="superadminanonce.php" class="admin-btn">Gestion des annonces</a>
        </div>
    <?php endif; ?>

    <div class="container">
        <div class="messages-section">
            <?php
            include 'templates/annonces.php';
            ?>
        </div>
        <?php if ($isAdmin): ?>
            <div class="add-food-container">
                <a href="addfood.php" class="add-food-btn"><i class="fa-solid fa-plus"></i> Ajouter un plat</a>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'templates/adminmessage.php'; ?>
    <?php include 'templates/clientmessage.php'; ?>

    <?php include 'templates/footer.php'; ?>
</body>

</html>