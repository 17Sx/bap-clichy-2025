<header>
    <div class="logo">
        <a href="home.php">        
            <img src="public/img/logo.png" alt="">
        </a>
    </div>
    
    <!-- Bouton burger pour mobile -->
    <div class="burger-menu">
        <span></span>
        <span></span>
        <span></span>
    </div>
    
    <div class="link">
        <nav>
            <ul>
                <li><a class="link-btn-f" href="index.php">Plats</a></li>
                <li><a class="link-btn-f" href="contact.php">Contact</a></li>
                <div class="connexion">
                    <?php 
                    if(isset($_SESSION['user_id']) && isset($_SESSION['pseudo'])) {
                        echo '<li><a href="logout.php" class="link-btn">Déconnexion</a></li>';
                    } else {
                        echo '<li><a href="login.php" class="link-btn">Se connecter </a><img src="public/icon/connexion.svg" alt="Icon de connexion"></li>';
                    }
                    ?>
                </div>
            </ul>
        </nav>
    </div>
</header>

<script src="js/script.js"></script>
