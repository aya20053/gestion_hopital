<?php
session_start(); // Démarrer la session

// Vérifier si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user_id'])) {
    header("Location: login_admin.php"); // Rediriger vers la page de connexion
    exit();
}

// Connexion à la base de données
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
    die("Erreur de connexion : " . $e->getMessage());
}

// Traitement de la recherche dynamique
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $query = "SELECT * FROM femmes_enceintes WHERE isadmin = 0 AND (nom LIKE :search OR prenom LIKE :search)";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['search' => "%$search%"]);
    $patients = $stmt->fetchAll();

    // Retourner les résultats au format JSON
    header('Content-Type: application/json');
    echo json_encode($patients);
    exit();
}

// Récupérer tous les patients (isadmin = 0) par défaut
$query = "SELECT * FROM femmes_enceintes WHERE isadmin = 0";
$stmt = $pdo->prepare($query);
$stmt->execute();
$patients = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Patients</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
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

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
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

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .action-buttons a {
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 4px;
            color: white;
            font-size: 14px;
        }

        .action-buttons .edit {
            background-color: #3498db;
        }

        .action-buttons .delete {
            background-color: #e74c3c;
        }

        .action-buttons .details {
            background-color: green;
        }

        .action-buttons a:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-users"></i> Liste des Patients</h1>

        <!-- Champ de recherche dynamique -->
        <div class="search-form">
            <input type="text" id="search" placeholder="Rechercher par nom ou prénom">
        </div>

        <!-- Tableau des patients -->
        <table id="patients-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patients as $patient): ?>
                    <tr>
                        <td><?= htmlspecialchars($patient['id']) ?></td>
                        <td><?= htmlspecialchars($patient['nom']) ?></td>
                        <td><?= htmlspecialchars($patient['prenom']) ?></td>
                        <td><?= htmlspecialchars($patient['email']) ?></td>
                        <td><?= htmlspecialchars($patient['telephone']) ?></td>
                        <td class="action-buttons">
                            <!-- Bouton Détails -->
                            <a href="details_patient.php?id=<?= $patient['id'] ?>" class="details">
                                <i class="fas fa-eye"></i> Détails
                            </a>

                            <!-- Bouton Modifier -->
                            <a href="edit_patient.php?id=<?= $patient['id'] ?>" class="edit">
                                <i class="fas fa-edit"></i> Modifier
                            </a>

                            <!-- Bouton Supprimer -->
                            <a href="delete_patient.php?id=<?= $patient['id'] ?>" class="delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce patient ?');">
                                <i class="fas fa-trash"></i> Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Fonction pour effectuer la recherche dynamique
        function searchPatients(query) {
            fetch(`patients.php?search=${query}`)
                .then(response => response.json())
                .then(data => {
                    const tbody = document.querySelector("#patients-table tbody");
                    tbody.innerHTML = ""; // Vider le tableau actuel

                    // Ajouter les nouveaux résultats
                    data.forEach(patient => {
                        const row = `
                            <tr>
                                <td>${patient.id}</td>
                                <td>${patient.nom}</td>
                                <td>${patient.prenom}</td>
                                <td>${patient.email}</td>
                                <td>${patient.telephone}</td>
                                <td class="action-buttons">
                                    <a href="details_patient.php?id=${patient.id}" class="details">
                                        <i class="fas fa-eye"></i> Détails
                                    </a>
                                    <a href="edit_patient.php?id=${patient.id}" class="edit">
                                        <i class="fas fa-edit"></i> Modifier
                                    </a>
                                    <a href="delete_patient.php?id=${patient.id}" class="delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce patient ?');">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </a>
                                </td>
                            </tr>
                        `;
                        tbody.innerHTML += row;
                    });
                })
                .catch(error => console.error('Erreur lors de la recherche :', error));
        }

        // Écouter les changements dans le champ de recherche
        document.getElementById('search').addEventListener('input', function (e) {
            const query = e.target.value.trim();
            if (query.length >= 2) { // Rechercher uniquement si 2 caractères ou plus
                searchPatients(query);
            } else if (query.length === 0) {
                // Recharger tous les patients si le champ est vide
                searchPatients('');
            }
        });
    </script>
</body>
</html>