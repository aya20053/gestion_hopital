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

// Initialiser la variable de recherche
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Préparer la requête SQL pour rechercher par nom de médecin ou description
$sql_notes = "
    SELECT n.note, n.date_ajout, n.description, CONCAT(m.prenom, ' ', m.nom) AS medecin
    FROM notes n
    JOIN medecins m ON n.medecin_id = m.id
    WHERE CONCAT(m.prenom, ' ', m.nom) LIKE ? OR n.description LIKE ?
    ORDER BY n.date_ajout DESC
";

$stmt = $conn->prepare($sql_notes);
$searchTerm = "%$searchTerm%"; // Pour inclure des correspondances partielles
$stmt->bind_param('ss', $searchTerm, $searchTerm); // Lier les deux paramètres
$stmt->execute();
$result_notes = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi Santé</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background-color: #e9ecef;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 30px;
        }
        .card {
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: scale(1.02);
        }
        .card-header {
            font-weight: bold;
            background-color: #007bff;
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 15px;
        }
        .note-text {
            font-size: 18px;
            color: #333;
        }
        .note-footer {
            font-size: 14px;
            color: #666;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .search-bar {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4"><i class="fas fa-notes-medical"></i> Suivi des Notes Médicales</h2>

    <!-- Formulaire de recherche -->
    <div class="search-bar">
        <div class="input-group">
            <input type="text" id="search" class="form-control" placeholder="Rechercher par nom de médecin ou description" value="<?php echo htmlspecialchars($searchTerm); ?>">
        </div>
    </div>

    <div id="notes-container">
        <?php if ($result_notes->num_rows > 0): ?>
            <?php while ($row = $result_notes->fetch_assoc()): ?>
            <div class="card mb-3">
                <div class="card-header">
                    Médecin : <span class="fw-bold"><?php echo htmlspecialchars($row['medecin']); ?></span>
                </div>
                <div class="card-body">
                    <p class="note-text">📝 "<?php echo nl2br(htmlspecialchars($row['description'])); ?>"</p>
                    <p>
                        <a href="./uploaded_notes/<?php echo htmlspecialchars($row['note']); ?>" target="_blank" class="btn btn-secondary">Voir le PDF</a>
                    </p>
                </div>
                <div class="card-footer note-footer">
                    Ajoutée le <?php echo htmlspecialchars($row['date_ajout']); ?>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center text-muted">Aucune note trouvée.</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    $('#search').on('input', function() {
        let searchTerm = $(this).val();
        $.ajax({
            url: 'fetch_notes.php', // Appel à la nouvelle page
            method: 'GET',
            data: { search: searchTerm },
            success: function(data) {
                $('#notes-container').html(data);
            }
        });
    });
});
</script>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>