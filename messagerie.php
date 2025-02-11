<?php
session_start();
ob_start();

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
$sql_patient = $conn->prepare("SELECT CONCAT(prenom, ' ', nom) AS patient FROM femmes_enceintes WHERE id = ?");
$sql_patient->bind_param("i", $femme_id);
$sql_patient->execute();
$result_patient = $sql_patient->get_result();
$patient_name = $result_patient->num_rows > 0 ? $result_patient->fetch_assoc()['patient'] : "";

// Récupérer les médecins disponibles
$sql_medecins = "SELECT id, nom FROM medecins";
$result_medecins = $conn->query($sql_medecins);

// Envoi du message
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $message = $conn->real_escape_string(trim($_POST['message']));
    $medecin_id = (int)$_POST['medecin_id'];

    // Vérifier si le message a déjà été envoyé
    $sql_check = $conn->prepare("SELECT * FROM messages WHERE message = ? AND femme_id = ? AND medecin_id = ?");
    $sql_check->bind_param("sii", $message, $femme_id, $medecin_id);
    $sql_check->execute();
    $result_check = $sql_check->get_result();

    if ($result_check->num_rows == 0) {
        $sql_message = $conn->prepare("INSERT INTO messages (message, femme_id, medecin_id) VALUES (?, ?, ?)");
        $sql_message->bind_param("sii", $message, $femme_id, $medecin_id);
        
        if ($sql_message->execute()) {
            echo "<p>Message envoyé avec succès !</p>";
        } else {
            echo "<p>Erreur lors de l'envoi du message : " . $conn->error . "</p>";
        }
    } else {
        echo "<p>Le message est déjà envoyé.</p>";
    }
}

// Historique des messages, y compris les réponses
$sql_historique = $conn->prepare("SELECT m.id, m.message, m.date_envoi, m.medecin_id, md.nom AS medecin, m.repondre FROM messages m JOIN medecins md ON m.medecin_id = md.id WHERE m.femme_id = ? ORDER BY m.date_envoi DESC");
$sql_historique->bind_param("i", $femme_id);
$sql_historique->execute();
$result_historique = $sql_historique->get_result();

// Mettre à jour le message
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_message'])) {
    // Récupérer l'ID du message à modifier depuis l'URL
    $message_id = (int)$_GET['edit'];
    $new_message = $conn->real_escape_string(trim($_POST['message']));
    $new_medecin_id = (int)$_POST['medecin_id'];

    $sql_update_message = $conn->prepare("UPDATE messages SET message = ?, medecin_id = ? WHERE id = ? AND femme_id = ?");
    $sql_update_message->bind_param("siii", $new_message, $new_medecin_id, $message_id, $femme_id);

    if ($sql_update_message->execute()) {
        // Redirection après mise à jour
        header("Location: messagerie.php");
        exit();
    } else {
        echo "<p>Erreur lors de la mise à jour du message : " . $conn->error . "</p>";
    }
}

// Supprimer un message
if (isset($_GET['delete'])) {
    $message_id = (int)$_GET['delete'];

    $sql_delete_message = $conn->prepare("DELETE FROM messages WHERE id = ? AND femme_id = ?");
    $sql_delete_message->bind_param("ii", $message_id, $femme_id);

    if ($sql_delete_message->execute()) {
        echo "<p>Message supprimé avec succès !</p>";
    } else {
        echo "<p>Erreur lors de la suppression du message : " . $conn->error . "</p>";
    }
}
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
            background-image: url('1.jpg');
            background-size: cover;
            background-position: center;
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
        .form-container button {
            background: #35b4c6;
            color: white;
            cursor: pointer;
        }
        .form-container button:hover {
            background: #1e88e5;
        }
        
        .message-history {
            margin: 20px;
            background: #f9f9f9;
        }
        .message-history h1, h2 {
            text-align: center;
            margin: 20px 0;
        }

        .message-item {
            background: #f9f9f9;
            padding: 10px;
            border-left: 5px solid #66bb6a;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0;
        }

        .message-content {
            flex: 1;
        }

        .btn-container {
            display: flex;
            gap: 10px;
        }

        .btn-edit, .btn-delete {
            text-decoration: none;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .btn-edit {
            background-color: #66bb6a;
        }

        .btn-edit:hover {
            background-color: #4caf50;
        }

        .btn-delete {
            background-color: #f44336;
        }

        .btn-delete:hover {
            background-color: #e53935;
        }

        .reply {
            background: #e3f2fd;
            padding: 10px;
            border-radius: 5px;
            margin-top: 5px;
            border-left: 5px solid #2196f3;
        }
    </style>
</head>
<body>

<div id="form-message" class="form-container">
    <form method="post" action="">
        <legend><h2><i class="fas fa-paper-plane"></i> Envoyer un message</h2></legend>
        <select name="medecin_id" required>
            <option value="">Choisissez un médecin</option>
            <?php while ($medecin = $result_medecins->fetch_assoc()): ?>
                <option value="<?php echo $medecin['id']; ?>"><?php echo htmlspecialchars($medecin['nom']); ?></option>
            <?php endwhile; ?>
        </select>
        
        <textarea name="message" rows="5" placeholder="Écrivez votre message ici..." required></textarea>
        
        <button type="submit" name="submit">Envoyer le message</button>
    </form>
</div>

<div class="message-history">
    <h2><i class="fas fa-history"></i> Historique des Messages</h2>
    <?php if ($result_historique->num_rows > 0): ?>
        <?php while ($row = $result_historique->fetch_assoc()): ?>
            <div class="message-item">
                <div class="message-content">
                    <strong>Le <?php echo htmlspecialchars($row['date_envoi']); ?></strong> :<br>
                    Message à <?php echo htmlspecialchars($row['medecin']); ?> :<br>
                    "<?php echo htmlspecialchars($row['message']); ?>"
                    
                    <?php if (!empty($row['repondre'])): ?>
                        <div class="reply">
                            <strong>Réponse :</strong><br>
                            "<?php echo htmlspecialchars($row['repondre']); ?>"
                        </div>
                    <?php endif; ?>
                </div>
                <div class="btn-container">
                    <a href="javascript:void(0);" onclick="toggleForm(<?php echo $row['id']; ?>)" class="btn-edit">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="messagerie.php?delete=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce message ?')">
                        <i class="fas fa-trash"></i> Supprimer
                    </a>
                </div>
                <div id="form-modification-<?php echo $row['id']; ?>" class="form-container" style="display:none;">
                    <h2>Modifier le message</h2>
                    <form method="post" action="messagerie.php?edit=<?php echo $row['id']; ?>">
                        <select name="medecin_id" required>
                            <option value="">Choisissez un médecin</option>
                            <?php
                            // Réinitialiser le pointeur des résultats des médecins
                            $result_medecins->data_seek(0);
                            while ($medecin = $result_medecins->fetch_assoc()): ?>
                                <option value="<?php echo $medecin['id']; ?>" <?php echo ($medecin['id'] == $row['medecin_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($medecin['nom']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <textarea name="message" rows="5" required><?php echo htmlspecialchars($row['message']); ?></textarea>
                        <button type="submit" name="update_message">Mettre à jour</button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Aucun message trouvé.</p>
    <?php endif; ?>
</div>
<?php
$conn->close();
?>
</body>
</html>