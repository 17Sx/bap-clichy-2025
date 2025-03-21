<?php if ($isAdmin): ?>

    <div id="adminPopup" class="admin-popup">
        <div class="admin-popup-content">
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

            <div class="popup-buttons">
                <label class="hide-message-label">
                    <input type="checkbox" id="hideAdminMessage"> Ne plus afficher ce message
                </label>
                <button class="close-popup-btn">J'ai compris</button>
            </div>
        </div>
    </div>

    <button id="openAdminPopup" class="admin-popup-btn">
        <i class="fa-solid fa-user-shield"></i> Guide Administrateur
    </button>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const popup = document.getElementById('adminPopup');
            const openBtn = document.getElementById('openAdminPopup');
            const closeBtn = document.querySelector('.close-popup-btn');
            const hideCheckbox = document.getElementById('hideAdminMessage');

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
                localStorage.setItem('hideAdminMessage', this.checked);
            });

            if (localStorage.getItem('hideAdminMessage') !== 'true') {
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