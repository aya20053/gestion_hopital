<?php
session_start();
include 'menu.php'; // Include your navigation menu
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
                <li>Total des patients : <strong>100</strong></li>
                <li>Total des médecins : <strong>20</strong></li>
                <li>Total des notes médicales : <strong>150</strong></li>
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
        type: 'bar', // You can change this to 'line', 'pie', etc.
        data: {
            labels: ['Patients', 'Médecins', 'Notes Médicales'],
            datasets: [{
                label: 'Total',
                data: [100, 20, 150], // Replace these values with dynamic data from your database
                backgroundColor: [
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
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