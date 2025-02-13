<?php
ob_start();
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

if (!isset($_SESSION['user_id'])) {
    die("Accès non autorisé. Veuillez vous connecter.");
}

$femme_id = $_SESSION['user_id'];

$sql_patient = "SELECT CONCAT(prenom, ' ', nom) AS patient FROM femmes_enceintes WHERE id = '$femme_id'";
$result_patient = $conn->query($sql_patient);
$patient_name = $result_patient->num_rows > 0 ? $result_patient->fetch_assoc()['patient'] : "";

// Récupérer la liste des médecins
$sql_medecins = "SELECT id, CONCAT(prenom, ' ', nom) AS medecin FROM medecins"; // Assurez-vous que la table medecins existe
$result_medecins = $conn->query($sql_medecins);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $date = $conn->real_escape_string($_POST['date']);
    $heure = $conn->real_escape_string($_POST['heure']);
    $medecin_id = $conn->real_escape_string($_POST['medecin_id']); // Récupérer le medecin_id
    $statut = "En attente";

    // Ajouter le timestamp actuel
    $created_at = date('Y-m-d H:i:s');

    $sql = "INSERT INTO rendezvous (date, heure, patient, statut, patient_id, created_at, medecin_id) 
            VALUES ('$date', '$heure', '$patient_name', '$statut', '$femme_id', '$created_at', '$medecin_id')";

    if ($conn->query($sql) === TRUE) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

$sql_rendezvous = "
    SELECT r.date, r.heure, r.statut, r.created_at, m.prenom, m.nom 
    FROM rendezvous r
    JOIN medecins m ON r.medecin_id = m.id 
    WHERE r.patient_id = '$femme_id' 
    ORDER BY r.date, r.heure";
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('1.jpg');
            background-size: cover ; /* L'image sera contenue dans la fenêtre */
            background-position: center;
            overflow: hidden;
            color: #333;
        }
        .container {
            width: 80%;
            max-width: 800px;
            margin: 0 auto;
        }
        h2 {
            text-align: center;
            color: #872341;
        }
        .form-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 100%;
            max-width: 400px;
            margin: 20px auto;
        }
        input, .btn {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border-radius: 6px;
            border: 1px solid #B39188;
            font-size: 16px;
            transition: 0.3s;
            background: #F8F9FA;
            color: #333;
        }
        .btn {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border-radius: 6px;
            border: none;
            font-size: 16px;
            transition: 0.3s;
            background: #872341;
            color: white;
            cursor: pointer;
            font-weight: bold;
        }
        .btn:hover {
            background: #B39188;
            color: #872341;
        }
        .historique {
            background: rgba(255, 255, 255, 0.85);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            text-align: left;
            margin-top: 20px;
            color: #333;
        }
        .historique p {
            margin: 10px 0;
            font-size: 16px;
            padding: 10px;
            background: #F8EDEB;
            border-left: 5px solid #872341;
            border-radius: 5px;
        }
        .btn-container {
            text-align: center;
            margin-top: 20px;
        }
        .btn-container button {
            margin: 10px;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #B39188;
            background: #872341;
            color: white;
            cursor: pointer;
        }
        .btn-container button:hover {
            background: #B39188;
            color: #872341;
        }

        .back-button {
            display: flex;
            justify-content: flex-start; /* Aligner à gauche */
            margin-top: 20px;
        }
        .back-button a {
            width: 100%;
            font-weight: bold;
            padding: 12px;
            background-color: #872341;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-align: center;
            text-decoration: none;
        }
        .back-button a:hover {
            background-color: #B39188;
            color:#872341;
        }
    </style>
</head>
<body>

<div class="container">
<div class="btn-container">
    <button id="showRendezvous" onclick="showRendezvous()">
        <i class="fas fa-calendar"></i> Afficher Rendez-vous
    </button>
    <button id="showHistorique" onclick="showHistorique()">
        <i class="fas fa-history"></i> Afficher Historique
    </button>
</div>


    <!-- Formulaire d'ajout de rendez-vous -->
    <div id="rendezvousForm" style="display: none;">
        <div class="form-container">
            <form method="post" action="">
                <legend><h2><i class="fas fa-calendar-plus"></i> Ajouter un Rendez-vous</h2></legend>
                <input type="date" name="date" required>
                <input type="time" name="heure" required>
                <select name="medecin_id" required>
                    <option value="">Sélectionnez un médecin</option>
                    <?php if ($result_medecins->num_rows > 0): ?>
                        <?php while ($row = $result_medecins->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($row['id']); ?>">
                                <?php echo htmlspecialchars($row['medecin']); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option value="">Aucun médecin disponible</option>
                    <?php endif; ?>
                </select>
                <button type="submit" name="submit" class="btn">
    <i class="fas fa-plus"></i> Ajouter
</button>

<div class="back-button">
    <a href="rendezvous.php"><i class="fas fa-arrow-left"></i> Annuler</a>
</div>

            </form>
        </div>
    </div>

    <!-- Historique des rendez-vous -->
    <div id="historique" style="display: none;">
        <div class="historique">
            <h2><i class="fas fa-history"></i> Historique des Rendez-vous</h2>
            <?php if (isset($result_rendezvous) && $result_rendezvous->num_rows > 0): ?>
                <?php while ($row = $result_rendezvous->fetch_assoc()): ?>
                    <p>
                        <strong>Date :</strong> <?php echo htmlspecialchars($row['date']); ?>,
                        <strong>Heure :</strong> <?php echo htmlspecialchars($row['heure']); ?>,
                        <strong>Statut :</strong> <?php echo htmlspecialchars($row['statut']); ?><br>
                        <strong>Médecin :</strong> <?php echo htmlspecialchars($row['prenom'] . ' ' . $row['nom']); ?><br>
                        <strong>Créé le :</strong> <?php echo htmlspecialchars($row['created_at']); ?>
                    </p>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Aucun rendez-vous trouvé.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function showRendezvous() {
        document.getElementById('rendezvousForm').style.display = 'block';
        document.getElementById('historique').style.display = 'none';
    }

    function showHistorique() {
        document.getElementById('rendezvousForm').style.display = 'none';
        document.getElementById('historique').style.display = 'block';
    }
</script>

</body>
</html>
