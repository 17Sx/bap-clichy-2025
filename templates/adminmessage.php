<?php if ($isAdmin): ?>

<div class="message-admin">
    <div class="admin-text">
        <h2>Vous êtes connecté sous un <br> compte Administrateur</h2>

        <p class="admin-feature">
            <strong>Fonctionnalité "Voir un aperçu"</strong><br>
            Vous avez la possibilité de consulter les informations d'un plat en cliquant simplement sur "Voir un aperçu". <br>
            Depuis ce menu vous pouvez :
            <ul>
                <li>Modifier les informations du plat</li>
                <li>Le supprimer de la liste</li>
                <li>Cocher la case "En rupture de stock" pour éviter de le supprimer</li>
            </ul>
        </p>

        <p class="admin-feature">
            <strong>Fonctionnalité "Ajouter un plat"</strong><br>
            Vous pouvez ajouter un ou plusieurs plats. <br>
            Pour ajouter un plat, renseignez :
            <ul>
                <li>Photo du plat (à télécharger)</li>
                <li>Nom du plat (avec ingrédients principaux)</li>
                <li>Liste des ingrédients</li>
                <li>Tags appropriés</li>
                <li>Quantité restante</li>
                <li>Nom et Adresse de l'école</li>
            </ul>
        </p>
        <p class="p-bottom">Une fois toutes les informations saisies, cliquez sur "Ajouter le plat". Il sera alors ajouté à la liste des plats disponibles, accessible aux clients ainsi qu'aux comptes administrateurs.</p>
    </div>


    <a href="addfood.php" class="add-food-btn"><i class="fa-solid fa-plus"></i> Ajouter un plat</a>

</div>


<?php endif;?>