<?php if ($isnotAdmin): ?>

    <button id="openClientPopup" class="client-popup-btn">
        <i class="fa-solid fa-user"></i> Guide Client
    </button>

    <div id="clientPopup" class="client-popup">
        <div class="client-popup-content">
            <span class="close-popup">&times;</span>

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
    </div>

    <style>
        .client-popup-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #2196F3;
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

        .client-popup-btn:hover {
            background-color: #1976D2;
            transform: translateY(-2px);
        }

        .client-popup {
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

        .client-popup-content {
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

        .client-text {
            margin-bottom: 20px;
        }

        .client-feature {
            margin: 15px 0;
            line-height: 1.6;
        }

        .client-feature ul {
            margin-left: 20px;
            margin-top: 10px;
        }

        .client-feature li {
            margin: 5px 0;
        }

        @media (max-width: 768px) {
            .client-popup-content {
                width: 95%;
                margin: 10% auto;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const popup = document.getElementById('clientPopup');
            const openBtn = document.getElementById('openClientPopup');
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