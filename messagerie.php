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

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    die("Accès non autorisé. Veuillez vous connecter.");
}

$femme_id = $_SESSION['user_id'];

// Récupérer le nom de la femme enceinte
$sql_patient = "SELECT CONCAT(prenom, ' ', nom) AS patient FROM femmes_enceintes WHERE id = '$femme_id'";
$result_patient = $conn->query($sql_patient);
$patient_name = $result_patient->num_rows > 0 ? $result_patient->fetch_assoc()['patient'] : "";

// Récupérer les médecins disponibles
$sql_medecins = "SELECT id, nom FROM medecins";
$result_medecins = $conn->query($sql_medecins);

// Envoi du message
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $message = $conn->real_escape_string($_POST['message']);
    $medecin_id = $_POST['medecin_id'];
    
    $sql_message = "INSERT INTO messages (message, femme_id, medecin_id) 
                    VALUES ('$message', '$femme_id', '$medecin_id')";
    
    if ($conn->query($sql_message) === TRUE) {
        echo "<p>Message envoyé avec succès !</p>";
    } else {
        echo "<p>Erreur lors de l'envoi du message : " . $conn->error . "</p>";
    }
}

// Historique des messages
$sql_historique = "SELECT m.message, m.date_envoi, md.nom AS medecin 
                   FROM messages m 
                   JOIN medecins md ON m.medecin_id = md.id 
                   WHERE m.femme_id = '$femme_id' 
                   ORDER BY m.date_envoi DESC";
$result_historique = $conn->query($sql_historique);

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messagerie</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
         body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    
      background-image: url('1.jpg'); /* Remplacez par le chemin de votre image */
      background-size: cover;
      background-position: center;
      overflow: hidden; /* Empêche le défilement de la page */
    }
        .form-container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
        }

        .form-container input, .form-container textarea, .form-container select, .form-container button {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        .message-history {
            margin-top: 30px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .message-history p {
            background: #f9f9f9;
            padding: 10px;
            margin: 10px 0;
            border-left: 5px solid #66bb6a;
            font-size: 16px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <form method="post" action="">
      <legend><h2>Envoyer un message</h2>
      </legend>
        <select name="medecin_id" required>
            <option value="">Choisissez un médecin</option>
            <?php while ($medecin = $result_medecins->fetch_assoc()): ?>
                <option value="<?php echo $medecin['id']; ?>"><?php echo $medecin['nom']; ?></option>
            <?php endwhile; ?>
        </select>
        
        <textarea name="message" rows="5" placeholder="Écrivez votre message ici..." required></textarea>
        
        <button type="submit" name="submit">Envoyer le message</button>
    </form>
</div>

<div class="message-history">
<h2>Historique des Messages</h2>

    <?php if ($result_historique->num_rows > 0): ?>
        <?php while ($row = $result_historique->fetch_assoc()): ?>
            <p><strong>Le <?php echo $row['date_envoi']; ?></strong> :<br>
               Message à <?php echo htmlspecialchars($row['medecin']); ?> :<br>
               "<?php echo htmlspecialchars($row['message']); ?>"</p>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Aucun message trouvé.</p>
    <?php endif; ?>
</div>

</body>
</html>
