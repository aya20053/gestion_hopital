<?php
session_start();
ob_start();

include 'menu_admin.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page
    exit();
}

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "clinique_bonheur";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fetch messages
$query = "
    SELECT m.*, f.prenom AS femme_prenom, f.nom AS femme_nom, d.prenom AS medecin_prenom, d.nom AS medecin_nom
    FROM messages m
    LEFT JOIN femmes_enceintes f ON m.femme_id = f.id
    LEFT JOIN medecins d ON m.medecin_id = d.id
";
$stmt = $pdo->prepare($query);
$stmt->execute();
$messages = $stmt->fetchAll();

// Handle search functionality
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $query = "
        SELECT m.*, f.prenom AS femme_prenom, f.nom AS femme_nom, d.prenom AS medecin_prenom, d.nom AS medecin_nom
        FROM messages m
        LEFT JOIN femmes_enceintes f ON m.femme_id = f.id
        LEFT JOIN medecins d ON m.medecin_id = d.id
        WHERE m.message LIKE :search OR f.nom LIKE :search OR d.nom LIKE :search
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['search' => "%$search%"]);
    $messages = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messagerie</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .search-form {
            margin-bottom: 20px;
        }
        .search-form input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #2c3e50;
            color: white;
        }
        table tr:hover {
            background-color: #f5f5f5;
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
    <div class="container">
        <h1><i class="fas fa-envelope"></i> Messagerie</h1>

        <!-- Search bar -->
        <div class="search-form">
            <form method="get" action="">
                <input type="text" name="search" placeholder="Rechercher des messages..." value="<?= isset($search) ? htmlspecialchars($search) : '' ?>">
            </form>
        </div>

        <!-- Messages table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Message</th>
                    <th>Date d'envoi</th>
                    <th>Statut</th>
                    <th>Patient</th>
                    <th>Médecin</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $msg): ?>
                    <tr>
                        <td><?= htmlspecialchars($msg['id']) ?></td>
                        <td><?= htmlspecialchars($msg['message']) ?></td>
                        <td><?= htmlspecialchars($msg['date_envoi']) ?></td>
                        <td><?= htmlspecialchars($msg['statut']) ?></td>
                        <td><?= htmlspecialchars($msg['femme_prenom'] . ' ' . $msg['femme_nom']) ?></td>
                        <td><?= htmlspecialchars($msg['medecin_prenom'] . ' ' . $msg['medecin_nom']) ?></td>
                        <td>
                            <a href="#" onclick="showReplyForm(<?= $msg['id'] ?>, '<?= htmlspecialchars($msg['femme_id']) ?>')">Répondre</a>
                            <div id="reply-form-<?= $msg['id'] ?>"></div>
                        </td>
                    </tr>
                    <?php if (!empty($msg['repondre'])): ?>
                    <tr>
                        <td colspan="7">
                            <div class="reply">
                                <strong>Réponse :</strong><br>
                                "<?php echo htmlspecialchars($msg['repondre']); ?>"
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script>
        function showReplyForm(messageId, femmeId) {
            const replyForm = `
                <div>
                    <h3>Répondre au patient</h3>
                    <form method="POST" action="send_reply.php">
                        <input type="hidden" name="femme_id" value="${femmeId}">
                        <input type="hidden" name="message_id" value="${messageId}">
                        <textarea name="reply" rows="4" required placeholder="Écrivez votre réponse ici..."></textarea>
                        <button type="submit">Envoyer</button>
                        <button type="button" onclick="document.getElementById('reply-form-${messageId}').innerHTML = ''">Annuler</button>
                    </form>
                </div>
            `;
            document.getElementById(`reply-form-${messageId}`).innerHTML = replyForm;
        }
    </script> 
</body>
</html>