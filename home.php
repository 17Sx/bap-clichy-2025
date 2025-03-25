<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Clichy | AntiGaspi</title>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/home.css">
</head>

<body>
  <?php
  session_start();
  require_once 'bdd.php';

  $stmt_total = $connexion->query("SELECT COUNT(*) as total FROM message");
  $total = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'];

  $stmt_claimed = $connexion->query("SELECT COUNT(*) as claimed FROM message WHERE is_claim = 1");
  $claimed = $stmt_claimed->fetch(PDO::FETCH_ASSOC)['claimed'];

  $percent_claimed = ($total > 0) ? round(($claimed / $total) * 100) : 0;
  $percent_unclaimed = 100 - $percent_claimed;
  ?>

  <?php include 'templates/header.php'; ?>

  <main>
    <section class="presentation">
      <div class="presentation-container">
        <div class="presentation-text">
          <h1>Découvrez les repas <br> disponibles à Clichy</h1>
          <p>Notre mission est de réduire le gaspillage alimentaire en connectant les associations avec les repas non consommés. Explorez notre plateforme pour voir les options disponibles et contribuer à cette cause importante.</p>
          <a href="index.php" class="presentation-btn">Explorer</a>
        </div>

        <div class="presentation-img">
          <img src="public/img/homeleft.png" alt="">
        </div>
    </section>

    <section class="function">
      <div class="function-text">
        <h2>Découvrez nos fonctionnalités pour un meilleur service</h2>
        <p>Notre Notre site vous permet de rechercher facilement les repas disponibles dans votre région. Inscrivez-vous pour recevoir des notifications sur les repas qui vous intéressent.</p>
      </div>

      <div class="functionnalities">
        <div class="functionnalities_box">
          <img src="./public/img/homemidleleft.png" alt="Placeholder Image">
          <h3>Recherche de repas disponible en temps réel</h3>
          <p>Trouvez rapidement les repas qui vous conviennent.</p>
        </div>
        <div class="functionnalities_box">
          <img src="./public/img/homemidlemidle.png" alt="Placeholder Image">
          <h3>Associations : Notifications personnalisées pour ne rien manquer</h3>
          <p>Recevez des alertes sur les repas disponibles.</p>
        </div>
        <div class="functionnalities_box">
          <img src="./public/img/homemidleright.png" alt="Placeholder Image">
          <h3>Cantines : Partagez vos repas avec les associations locales</h3>
          <p>Contribuez à réduire le gaspillage alimentaire au sein de votre cantine.</p>
        </div>
      </div>

      <a class="learn_more" href="faq.php">En savoir plus</a>
    </section>

    <section class="stat">
      <div class="width_50">
        <h3 class="stat_title">Statistiques en temps réel sur les repas disponibles à Clichy.</h3>
      </div>

      <div class="stat_box width_50">
        <h4>Découvrez combien de repas sont disponibles chaque jour. Nos statistiques aident les associations à mieux planifier leurs récupérations.</h4>
        <div class="meal_stat">
          <div class="meal_stat_box">
            <h3><?php echo $percent_claimed; ?>%</h3>
            <p>Repas récupérés par les associations partenaires.</p>
          </div>
          <div class="meal_stat_box">
            <h3><?php echo $percent_unclaimed; ?>%</h3>
            <p>Repas disponibles pour les associations partenaires.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="work">
      <div class="work-text">
        <h2>Comment fonctionne notre plateforme de repas?</h2>
        <p>Notre Notre site vous permet de rechercher facilement les repas disponibles dans votre région. Inscrivez-vous pour recevoir des notifications sur les repas qui vous intéressent.</p>
      </div>
      <div class="functionnalities">
        <div class="functionnalities_box">
          <img src="./public/img/homebottomleft.png" alt="Placeholder Image">
          <h3>Processus de signalement des repas disponibles</h3>
          <p>Les restaurants s'inscrivent et indiquent les plats restants.</p>
        </div>
        <div class="functionnalities_box">
          <img src="./public/img/homebottommidle.png" alt="Placeholder Image">
          <h3>Processus de signalement des repas disponibles</h3>
          <p>Les restaurants s'inscrivent et indiquent les plats restants.</p>
        </div>
        <div class="functionnalities_box">
          <img src="./public/img/homebottomright.png" alt="Placeholder Image">
          <h3>Processus de signalement des repas disponibles</h3>
          <p>Les restaurants s'inscrivent et indiquent les plats restants.</p>
        </div>
      </div>

      <a class="learn_more" href="faq.php">En savoir plus</a>
    </section>


    <section class="informed">
      <div class="informed-container">
        <div>
          <h2>Restez Informé des repas</h2>
          <p>Abonnez-vous pour des mises à jour régulières.</p>
        </div>
        <div>
          <div class="informed-input">
            <input type="email" placeholder="Votre adresse e-mail">
            <button class="register">S'inscrire</button>
          </div>
          <p>En cliquant sur S'inscrire, vous acceptez nos Conditions Générales.</p>
        </div>
      </div>
    </section>
  </main>

  <?php include 'templates/footer.php'; ?>

</body>

</html>