<?php if ($isnotAdmin): ?>


    <div class="message-client">
        <div class="client-text">
            <h2>Vous êtes connecté sous un <br> compte Client</h2>

            <p class="client-feature">
                <strong>Fonctionnalité "Voir le plat"</strong><br>
                Vous avez la possibilité de consulter les informations d'un plat en cliquant sur "Voir un aperçu". <br>
                Depuis ce menu, vous pouvez :
                <ul>
                    <li>Visualiser les détails du plat (ingrédients, photo, quantités disponibles, etc.)</li>
                    <li>Vérifier s'il est en stock</li>
                    <li>Cliquer sur le bouton "Commander" si vous souhaitez réserver ce plat</li>
                </ul>
            </p>

            <p class="client-feature">
                <strong>Fonctionnalité "Commander un plat"</strong><br>
                Lorsque vous cliquez sur "Commander", vous serez redirigé vers une page où vous devrez renseigner : <br>
                <ul>
                    <li>Quantité souhaitée (en chiffre, par exemple : 60 plats)</li>
                    <li>Nom de votre association</li>
                    <li>Date et horaires de récupération des plats</li>
                </ul>
            </p>
            <p class="p-bottom">Une fois ces informations saisies, cliquez sur "Commander". Vous aurez alors un aperçu de votre commande et devrez cliquer sur "Confirmer commande" pour finaliser votre demande. Votre commande sera alors prise en compte et transmise aux administrateurs.</p>
        </div>
    </div>
<?php endif; ?>
