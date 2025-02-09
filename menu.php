<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu à Gauche</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
      
        /* Styles pour le menu */
        .menu {
            position: fixed;
            top: 0;
            left: -250px; /* Cache le menu par défaut */
            width: 250px;
            height: 100vh;
            background-color:#529ba4 ;
            color: white;
            transition: left 0.3s ease;
            z-index: 1000;
        }
   
        .menu-item img {
    max-width: 100%; /* Adaptation à la taille du conteneur */
    height: auto; /* Conserve les proportions */
}
        .menu.open {
            left: 0; /* Affiche le menu */
        }

        .menu-header {
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .menu-header h2 {
            margin: 0;
            font-size: 20px;
        }

        .menu-header .close-btn {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
        }

        .menu-items {
            padding: 20px;
        }

        .menu-item {
            margin-bottom: 15px;
        }

        .menu-item a {
            display: flex;
            align-items: center;
            color: white;
            text-decoration: none;
            font-size: 16px;
        }

       
        .menu-item i {
            margin-right: 10px;
            font-size: 18px;
        }

        .conseil {
            margin-top: 5px;
            font-size: 12px;
            color: #bdc3c7;
            padding-left: 28px; /* Pour aligner avec l'icône */
        }

        /* Bouton pour ouvrir le menu */
        .open-menu-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: #529ba4 ;
            color: white;
            border: none;
            padding: 10px 15px;
            font-size: 20px;
            cursor: pointer;
            z-index: 1001;
            transition: left 0.3s ease; /* Animation pour le déplacement */
        }

        .open-menu-btn.menu-open {
            left: 270px; /* Déplace le bouton lorsque le menu est ouvert */
        }

        .open-menu-btn:hover {
            background-color:#336761;
        }        /* Styles pour le menu */
        .menu {
            position: fixed;
            top: 0;
            left: -250px;
            width: 250px;
            height: 100vh;
            background-color: #336761;
            color: white;
            transition: left 0.3s ease;
            z-index: 1000;
            padding-top: 20px;
        }

        .menu.open {
            left: 0;
        }

        .menu-items {
            padding: 10px;
        }

        .menu-item {
            margin-bottom: -6px;
            padding: 10px;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .menu-item a {
            display: flex;
            align-items: center;
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold; /* Texte en gras */
            transition: color 0.3s ease;
        }

        .menu-item:hover {
            background: rgba(255, 255, 255, 0.2); /* Effet survol */
        }

        .menu-item i {
            margin-right: 10px;
            font-size: 18px;
        }

        .conseil {
            margin-top: 3px;
            font-size: 12px;
            color: #bdc3c7;
            padding-left: 28px;
        }

        /* Bouton pour ouvrir le menu */
        .open-menu-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: #336761;
            color: white;
            border: none;
            padding: 10px 15px;
            font-size: 20px;
            cursor: pointer;
            z-index: 1001;
            transition: left 0.3s ease, background 0.3s ease;
            border-radius: 5px;
        }

        .open-menu-btn.menu-open {
            left: 270px;
        }

        .open-menu-btn:hover {
            background-color: #1f4a4d;
        }

    </style>
</head>
<body>
    <!-- Bouton pour ouvrir le menu -->
    <button class="open-menu-btn" id="open-menu-btn" onclick="toggleMenu()">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Menu à gauche -->
    <div class="menu" id="menu">
        <div class="menu-header">
           
        </div>
        <div class="menu-items">
        <div class="menu-item">
            <img src="lo.png" alt="Logo Hôpital" />
            </div>
                <div class="menu-item">
                <a href="modifier_profil.php">
                    <i class="fas fa-user-edit"></i> Modifier mon profil
                </a>
                <div class="conseil">Assurez-vous que votre profil est à jour.</div>
            </div>
            <div class="menu-item">
                <a href="rendezvous.php">
                    <i class="fas fa-calendar-check"></i> Prendre un rendez-vous
                </a>
                <div class="conseil">Planifiez vos consultations en ligne.</div>
            </div>
            <div class="menu-item">
                <a href="messagerie.php">
                    <i class="fas fa-envelope"></i> Messagerie
                </a>
                <div class="conseil">Communiquez avec votre médecin.</div>
            </div>
            <div class="menu-item">
                <a href="suivi_sante.php">
                    <i class="fas fa-heartbeat"></i> Suivi de santé
                </a>
                <div class="conseil">Suivez vos indicateurs de santé.</div>
            </div>
            <div class="menu-item">
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Se déconnecter
                </a>
                <div class="conseil">Déconnectez-vous en toute sécurité.</div>
            </div>
        </div>
    </div>

    <script>
        // Fonction pour ouvrir/fermer le menu
        function toggleMenu() {
            const menu = document.getElementById('menu');
            const openMenuBtn = document.getElementById('open-menu-btn');

            // Bascule l'état du menu
            menu.classList.toggle('open');

            // Bascule la classe pour déplacer le bouton
            openMenuBtn.classList.toggle('menu-open');
        }
    </script>
</body>
</html>