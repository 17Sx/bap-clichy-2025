<?php if ($isnotAdmin): ?>

    <button id="openClientPopup" class="client-popup-btn">
        <i class="fa-solid fa-user"></i> Guide Client
    </button>

    <div id="clientPopup" class="client-popup">
        <div class="client-popup-content">
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

            <div class="popup-buttons">
                <label class="hide-message-label">
                    <input type="checkbox" id="hideClientMessage"> Ne plus afficher ce message
                </label>
                <button class="close-popup-btn">J'ai compris</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const popup = document.getElementById('clientPopup');
            const openBtn = document.getElementById('openClientPopup');
            const closeBtn = document.querySelector('.close-popup-btn');
            const hideCheckbox = document.getElementById('hideClientMessage');

            function showPopup() {
                popup.style.display = 'block';
                document.body.style.overflow = 'hidden';
                setTimeout(() => {
                    popup.classList.add('show');
                }, 10);
            }

            function hidePopup() {
                popup.classList.remove('show');
                setTimeout(() => {
                    popup.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }, 300);
            }

            hideCheckbox.addEventListener('change', function() {
                localStorage.setItem('hideClientMessage', this.checked);
            });

            if (localStorage.getItem('hideClientMessage') !== 'true') {
                showPopup();
            }

            openBtn.addEventListener('click', showPopup);
            closeBtn.addEventListener('click', hidePopup);

            window.addEventListener('click', function(event) {
                if (event.target === popup) {
                    hidePopup();
                }
            });
        });
    </script>

<?php endif; ?>