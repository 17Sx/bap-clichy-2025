<?php if ($isAdmin): ?>

    <button id="openAdminPopup" class="admin-popup-btn">
        <i class="fa-solid fa-user-shield"></i> Guide Administrateur
    </button>

    <div id="adminPopup" class="admin-popup">
        <div class="admin-popup-content">
            <span class="close-popup">&times;</span>

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
    </div>

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

        .add-food-btn {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        .add-food-btn:hover {
            background-color: #45a049;
        }

        @media (max-width: 768px) {
            .admin-popup-content {
                width: 95%;
                margin: 10% auto;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const popup = document.getElementById('adminPopup');
            const openBtn = document.getElementById('openAdminPopup');
            const closeBtn = document.querySelector('.close-popup');

            openBtn.addEventListener('click', function() {
                popup.style.display = 'block';
                document.body.style.overflow = 'hidden';
            });

            closeBtn.addEventListener('click', function() {
                popup.style.display = 'none';
                document.body.style.overflow = 'auto';
            });

            window.addEventListener('click', function(event) {
                if (event.target === popup) {
                    popup.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            });
        });
    </script>

<?php endif; ?>