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
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .client-popup.show {
            opacity: 1;
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
            transform: translateY(-20px);
            transition: transform 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .client-popup.show .client-popup-content {
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