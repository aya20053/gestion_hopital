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

// Check for AJAX search request
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
    $sql_notes = "
        SELECT n.note, n.date_ajout, n.description, CONCAT(m.prenom, ' ', m.nom) AS medecin
        FROM notes n
        JOIN medecins m ON n.medecin_id = m.id
        WHERE CONCAT(m.prenom, ' ', m.nom) LIKE ? OR n.description LIKE ?
        ORDER BY n.date_ajout DESC
    ";

    $stmt = $conn->prepare($sql_notes);
    $searchTerm = "%$searchTerm%"; // For partial matches
    $stmt->bind_param('ss', $searchTerm, $searchTerm); // Bind parameters
    $stmt->execute();
    $result_notes = $stmt->get_result();

    $notes = [];
    while ($row = $result_notes->fetch_assoc()) {
        $notes[] = $row;
    }
    echo json_encode($notes); // Return results as JSON
    exit(); // Stop further execution
}

// Initial search variable
$searchTerm = isset($_POST['search']) ? $_POST['search'] : '';

// Prepare SQL query to fetch notes
$sql_notes = "
    SELECT n.note, n.date_ajout, n.description, CONCAT(m.prenom, ' ', m.nom) AS medecin
    FROM notes n
    JOIN medecins m ON n.medecin_id = m.id
    WHERE CONCAT(m.prenom, ' ', m.nom) LIKE ? OR n.description LIKE ?
    ORDER BY n.date_ajout DESC
";

$stmt = $conn->prepare($sql_notes);
$searchTerm = "%$searchTerm%"; // For partial matches
$stmt->bind_param('ss', $searchTerm, $searchTerm); // Bind parameters
$stmt->execute();
$result_notes = $stmt->get_result();

if (!$result_notes) {
    die("Erreur lors de la récupération des notes: " . $conn->error);
}
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
    <form method="POST" class="search-bar">
        <div class="input-group">
            <input type="text" id="search" class="form-control" placeholder="Rechercher par nom de médecin ou description" value="<?php echo htmlspecialchars($searchTerm); ?>">
            <button class="btn btn-primary" type="submit">Rechercher</button>
        </div>
    </form>

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
                        <a href="<?php echo htmlspecialchars($row['note']); ?>" target="_blank" class="btn btn-secondary">Voir le PDF</a>
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
            url: '', // Current page
            method: 'GET',
            data: { search: searchTerm },
            success: function(data) {
                let notes = JSON.parse(data);
                let resultsHTML = '';
                if (notes.length > 0) {
                    notes.forEach(note => {
                        resultsHTML += `
                            <div class="card mb-3">
                                <div class="card-header">
                                    Médecin : <span class="fw-bold">${note.medecin}</span>
                                </div>
                                <div class="card-body">
                                    <p class="note-text">📝 "${note.description}"</p>
                                    <p>
                                        <a href="${note.note}" target="_blank" class="btn btn-secondary">Voir le PDF</a>
                                    </p>
                                </div>
                                <div class="card-footer note-footer">
                                    Ajoutée le ${note.date_ajout}
                                </div>
                            </div>
                        `;
                    });
                } else {
                    resultsHTML = '<p class="text-center text-muted">Aucune note trouvée.</p>';
                }
                $('#notes-container').html(resultsHTML);
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