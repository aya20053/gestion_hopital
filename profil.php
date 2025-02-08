<?php
session_start();
include 'menu.php';
// Vérifie si la clé existe dans la session, sinon initialise à 0
$profilComplet = isset($_SESSION['profil_complet']) ? $_SESSION['profil_complet'] : 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord</title>
    <style>
        body {
            margin-top: 60px;
        }
        .alert {
            background: #ffcc00;
            padding: 15px;
            color: #333;
            text-align: center;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>Bienvenue,!</h1>

    <?php if ($profilComplet == 0): ?>
        <div class="alert">
            ⚠️ Votre dossier est incomplet. Veuillez <a href="completer_profil.php">le compléter ici</a>.
        </div>
    <?php endif; ?>

</body>
</html>
