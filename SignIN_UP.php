
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./src/SignIN_UP.css">
    <title>Se connecter</title>
</head>
<body>
    <?php require_once('header.php');?>
    <div class="contenaire">
        <form class="connexion" action="connexion.php" method="POST">
            <h1 class="action">Connexion : </h1>
            <?php   
                if (isset($_GET['error'])) {
                    echo "<h3 class='error'>" . htmlspecialchars($_GET['error']) . "</h3>";
                }
            ?>
            <hr>
            <input class="info user"type="text" name="user" placeholder="Entrer un nom d'utilisateur" style="display: none;">
            <br>
            <input class="info" type="text" name="email" placeholder="Entrer un email">
            <br>
            <input class="info" type="password" name="password" placeholder="Entrer un mot de passe">
            <br>
            <hr>
            <input class="button" type="submit" value="Se connecter">
        </form>

        <div class="inscription">
            <h1 class="title">Pas de compte ?</h1>
            <hr>
            <p class="accroche">Créer vous un compte gratuitement et commencer a utiliser CanneLife</p>
            <hr>
            <button class="button change">Rejoignez-nous</button>
        </div>


<!-- 
        <form class="inscription" action="inscription.php" method="POST">
        <label for="user">entrer un user</label>
        <input type="text" name="user">
        <br>
        <label for="email">entrer un email</label>
        <input type="text" name="email">
        <br>
        <label for="mdp">entrer un mot de passe</label>
        <input type="password" name="password">
        <br>
        <input type="submit"> -->
    </form>
    </div>
    
    <script src="./src/form.js"></script>
</body>
</html>