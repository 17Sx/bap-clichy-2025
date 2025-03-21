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

    <style>
        .admin-popup-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #4CAF50;
            color: white;
            padding: 15px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .admin-popup-btn:hover {
            background-color: #45a049;
            transform: translateY(-2px);
        }

        .admin-popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1001;
            overflow-y: auto;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .admin-popup.show {
            opacity: 1;
        }

        .admin-popup-content {
            position: relative;
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            width: 80%;
            max-width: 800px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transform: translateY(-20px);
            transition: transform 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .admin-popup.show .admin-popup-content {
            transform: translateY(0);
        }

        .close-popup {
            position: absolute;
            right: 20px;
            top: 10px;
            font-size: 28px;
            font-weight: bold;
            color: #666;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close-popup:hover {
            color: #000;
        }

        .admin-text {
            margin-bottom: 20px;
        }

        .admin-feature {
            margin: 15px 0;
            line-height: 1.6;
        }

        .admin-feature ul {
            margin-left: 20px;
            margin-top: 10px;
        }

        .admin-feature li {
            margin: 5px 0;
        }

        @media (max-width: 768px) {
            .admin-popup-content {
                width: 95%;
                margin: 10% auto;
            }
        }

        .popup-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            width: 80%;
            justify-content: space-between;
        }

        .hide-message-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            color: #666;
            font-size: 14px;
        }

        .hide-message-label input[type="checkbox"] {
            width: 16px;
            height: 16px;
        }

        .close-popup-btn {
            background-color: var(--blue);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .close-popup-btn:hover {
            border: 1px solid var(--blue);
            color: var(--blue);
            background-color: white;
        }

        @media (max-width: 768px) {
            .popup-buttons {
                flex-direction: column;
                gap: 15px;
                align-items: center;
            }
        }
    </style>

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

            // Gérer le changement de la case à cocher
            hideCheckbox.addEventListener('change', function() {
                localStorage.setItem('hideAdminMessage', this.checked);
            });

            // Ouvrir automatiquement la popup au chargement si le message n'est pas masqué
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