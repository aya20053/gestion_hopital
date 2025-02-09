<?php
session_start();

// Connexion à la base de données
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "clinique_bonheur";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    die("Accès non autorisé. Veuillez vous connecter.");
}

$femme_id = $_SESSION['user_id'];

$sql_patient = "SELECT CONCAT(prenom, ' ', nom) AS patient FROM femmes_enceintes WHERE id = '$femme_id'";
$result_patient = $conn->query($sql_patient);
$patient_name = $result_patient->num_rows > 0 ? $result_patient->fetch_assoc()['patient'] : "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $date = $conn->real_escape_string($_POST['date']);
    $heure = $conn->real_escape_string($_POST['heure']);
    $statut = "En attente";

    $sql = "INSERT INTO rendezvous (date, heure, patient, statut, patient_id) 
            VALUES ('$date', '$heure', '$patient_name', '$statut', '$femme_id')";

    if ($conn->query($sql) === TRUE) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

$sql_rendezvous = "SELECT date, heure, statut FROM rendezvous WHERE patient_id = '$femme_id' ORDER BY date, heure";
$result_rendezvous = $conn->query($sql_rendezvous);

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rendez-vous</title>
    <style>
        body {
            font-family: "Arial", sans-serif;
            background-color: #f4f4f4;
            color: black;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .form-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
            margin-bottom: 20px;
        }

        input, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
        }

        button {
            background: #2575fc;
            color: white;
            cursor: pointer;
            transition: 0.3s;
            border: none;
        }

        button:hover {
            background: #6a11cb;
        }

        .historique {
            width: 100%;
            max-width: 600px;
            text-align: left;
        }

        .historique p {
            margin: 5px 0;
            font-size: 16px;
        }
    </style>
</head>
<body>

<h2>Ajouter un Rendez-vous</h2>
<div class="form-container">
    <form method="post" action="">
        <input type="date" name="date" required>
        <input type="time" name="heure" required>
        <button type="submit" name="submit">Ajouter</button>
    </form>
</div>

<h2>Historique des Rendez-vous</h2>
<div class="historique">
    <?php if (isset($result_rendezvous) && $result_rendezvous->num_rows > 0): ?>
        <?php while ($row = $result_rendezvous->fetch_assoc()): ?>
            <p><strong>Date :</strong> <?php echo htmlspecialchars($row['date']); ?>, 
               <strong>Heure :</strong> <?php echo htmlspecialchars($row['heure']); ?>, 
               <strong>Statut :</strong> <?php echo htmlspecialchars($row['statut']); ?></p>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Aucun rendez-vous trouvé.</p>
    <?php endif; ?>
</div>

</body>
</html>
