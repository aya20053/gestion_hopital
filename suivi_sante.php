<?php
session_start();
include 'menu.php';

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "clinique_bonheur";

// Connexion à la base de données
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}



// Récupérer toutes les notes de la base de données
$sql_notes = "SELECT n.note, n.date_ajout
              FROM notes n 
              ORDER BY n.date_ajout DESC";

$result_notes = $conn->query($sql_notes);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi Santé</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 30px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            font-weight: bold;
            background-color: #007bff;
            color: white;
            border-radius: 15px 15px 0 0;
        }
        .note-text {
            font-size: 16px;
            color: #333;
        }
        .note-footer {
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">📋 Suivi des Notes Médicales</h2>

    <?php if ($result_notes && $result_notes->num_rows > 0): ?>
        <?php while ($row = $result_notes->fetch_assoc()): ?>
        <div class="card mb-3">
            <div class="card-header">
                Patient : <span class="fw-bold"><?php echo htmlspecialchars($row['patient']); ?></span>
            </div>
            <div class="card-body">
                <p class="note-text">📝 "<?php echo nl2br(htmlspecialchars($row['note'])); ?>"</p>
            </div>
            <div class="card-footer note-footer">
                Rédigée par <strong><?php echo htmlspecialchars($row['medecin']); ?></strong> le <?php echo htmlspecialchars($row['date_ajout']); ?>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-center text-muted">Aucune note trouvée.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>