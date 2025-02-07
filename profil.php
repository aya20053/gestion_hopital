<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$profilComplet = $_SESSION['profil_complet'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord</title>
    <style>
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
    <h1>Bienvenue, <?php echo $_SESSION['nom']; ?> !</h1>

    <?php if ($profilComplet == 0): ?>
        <div class="alert">
            ⚠️ Votre dossier est incomplet. Veuillez <a href="completer_profil.php">le compléter ici</a>.
        </div>
    <?php endif; ?>
    
    <p><a href="logout.php">Se déconnecter</a></p>
</body>
</html>
