<?php
session_start();
include 'menu.php'; // Include your navigation menu

// Database connection parameters
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "clinique_bonheur";

// Connect to the database
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get the total number of patients
$sql_patients = "SELECT COUNT(*) AS total FROM femmes_enceintes";
$result_patients = $conn->query($sql_patients);
$total_patients = $result_patients->fetch_assoc()['total'];

// Query to get the total number of doctors
$sql_medecins = "SELECT COUNT(*) AS total FROM medecins";
$result_medecins = $conn->query($sql_medecins);
$total_medecins = $result_medecins->fetch_assoc()['total'];

// Query to get the total number of medical notes
$sql_notes = "SELECT COUNT(*) AS total FROM notes";
$result_notes = $conn->query($sql_notes);
$total_notes = $result_notes->fetch_assoc()['total'];

$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques</title>
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
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4"><i class="fas fa-chart-line"></i> Statistiques</h2>

    <div class="card">
        <div class="card-header">
            Résumé des Données
        </div>
        <div class="card-body">
            <p>Voici quelques statistiques importantes :</p>
            <ul>
                <li>Total des patients : <strong><?php echo $total_patients; ?></strong></li>
                <li>Total des médecins : <strong><?php echo $total_medecins; ?></strong></li>
                <li>Total des notes médicales : <strong><?php echo $total_notes; ?></strong></li>
            </ul>
        </div>
    </div>

    <div class="mt-4">
        <h4>Graphiques Statistiques</h4>
        <p>Les graphiques suivants illustrent les données collectées :</p>
        
        <canvas id="myChart" width="400" height="200"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('myChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'bar', // Change this to 'line', 'pie', etc. if needed
        data: {
            labels: ['Patients', 'Médecins', 'Notes Médicales'],
            datasets: [{
                
                data: [<?php echo $total_patients; ?>, <?php echo $total_medecins; ?>, <?php echo $total_notes; ?>], // Use dynamic data from the database
                backgroundColor: [
                    'rgba(11, 87, 87, 0.6)',
                    'rgba(153, 102, 255, 0.6)',
                    'rgba(255, 159, 64, 0.7)'
                ],
                borderColor: [
                    'rgb(3, 65, 65)',
                    'rgb(89, 42, 184)',
                    'rgb(150, 83, 17)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
</body>
</html>