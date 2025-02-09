<?php
session_start();
include 'menu.php';

// Connexion à la base de données
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "clinique_bonheur";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// Récupérer l'ID de l'utilisateur depuis la session
$user_id = $_SESSION['user_id'];

// Récupérer la valeur de `profil_complet` depuis la base de données
$sql = "SELECT profil_complet FROM femmes_enceintes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION['profil_complet'] = $row['profil_complet']; // Mettre à jour la session
} else {
    $_SESSION['profil_complet'] = 0; // Valeur par défaut si l'utilisateur n'est pas trouvé
}

$profilComplet = $_SESSION['profil_complet']; // Utiliser la valeur de la session
$stmt->close();
$conn->close();
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

    <?php if ($profilComplet === 0): ?>
        <div class="alert">
            ⚠️ Votre dossier est incomplet. Veuillez <a href="completer_profil.php">le compléter ici</a>.
        </div>
    <?php endif; ?>

</body>
</html>