
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

// Récupérer l'ID de l'utilisateur depuis la session
$user_id = $_SESSION['user_id'];

// Récupérer les données de l'utilisateur
$sql = "SELECT * FROM femmes_enceintes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc(); // Récupérer les données sous forme de tableau associatif
$stmt->close();

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Valider et nettoyer les données
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);
    $date_naissance = htmlspecialchars($_POST['date_naissance']);
    $telephone = htmlspecialchars($_POST['telephone']);
    $adresse = htmlspecialchars($_POST['adresse']);
    $groupe_sanguin = htmlspecialchars($_POST['groupe_sanguin']);
    $date_dernieres_regles = htmlspecialchars($_POST['date_dernieres_regles']);
    $date_prevue_accouchement = htmlspecialchars($_POST['date_prevue_accouchement']);
    $nombre_grossesses_precedentes = htmlspecialchars($_POST['nombre_grossesses_precedentes']);
    $antecedents_medicaux = htmlspecialchars($_POST['antecedents_medicaux']);
    $allergies = htmlspecialchars($_POST['allergies']);

    // Mettre à jour les données dans la base de données
    $sql = "UPDATE femmes_enceintes SET 
        nom = ?, 
        prenom = ?, 
        email = ?, 
        date_naissance = ?, 
        telephone = ?, 
        adresse = ?, 
        groupe_sanguin = ?, 
        date_dernieres_regles = ?, 
        date_prevue_accouchement = ?, 
        nombre_grossesses_precedentes = ?, 
        antecedents_medicaux = ?, 
        allergies = ? 
        WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssssi", 
        $nom, $prenom, $email, $date_naissance, $telephone, $adresse, $groupe_sanguin, 
        $date_dernieres_regles, $date_prevue_accouchement, $nombre_grossesses_precedentes, 
        $antecedents_medicaux, $allergies, $user_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = 'Profil mis à jour avec succès.';
    } else {
        $_SESSION['message'] = 'Erreur lors de la mise à jour du profil.';
    }
    $stmt->close();

    // Rediriger pour éviter la soumission multiple du formulaire
   
    exit;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Profil</title>
    <link rel="stylesheet" href="styles.css">

    
    <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background-image: url('1.jpg'); /* Remplacez par le chemin de votre image */
      background-size: cover;
      background-position: center;
      overflow: hidden; /* Empêche le défilement de la page */
    }

    form {
      max-width: 700px;
      width: 90%; /* Ajustement pour les petits écrans */
      max-height: 80vh; /* Limite la hauteur du formulaire */
      padding: 30px;
      background-color: rgba(255, 255, 255, 0.4); /* Transparence */
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
      backdrop-filter: blur(5px); /* Effet de flou */
      overflow-y: auto; /* Ajoute un défilement vertical si nécessaire */
    }

    label {
      display: block;
      margin-bottom: 10px;
      font-weight: 600;
      color: #333;
    }

    input[type="text"],
    input[type="date"],
    input[type="number"],
    textarea {
      width: 100%;
      padding: 12px;
      margin-bottom: 25px;
      border: 1px solid #ced4da;
      border-radius: 5px;
      box-sizing: border-box;
      background-color: rgba(255, 255, 255, 0.8); /* Légère transparence pour les champs */
    }

    input:focus,
    textarea:focus {
      border-color: #35b4c6;
      outline: none;
    }

    button {
      display: inline-block;
      padding: 12px 25px;
      background-color: #35b4c6;
      color: #ffffff;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color:#529ba4;
    }
  </style>
</head>
<body>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert">
            <?= htmlspecialchars($_SESSION['message']); ?>
            <?php unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <form action="modifier_profil.php" method="POST">
        <legend>    <h1>Modifier le Profil</h1>
        </legend>
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($user['nom'] ?? ''); ?>" required>

        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($user['prenom'] ?? ''); ?>" required>

        <label for="email">Email :</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? ''); ?>" required>

        <label for="date_naissance">Date de Naissance :</label>
        <input type="date" id="date_naissance" name="date_naissance" value="<?= htmlspecialchars($user['date_naissance'] ?? ''); ?>" required>

        <label for="telephone">Téléphone :</label>
        <input type="text" id="telephone" name="telephone" value="<?= htmlspecialchars($user['telephone'] ?? ''); ?>" required>

        <label for="adresse">Adresse :</label>
        <textarea id="adresse" name="adresse" required><?= htmlspecialchars($user['adresse'] ?? ''); ?></textarea>

        <label for="groupe_sanguin">Groupe Sanguin :</label>
        <input type="text" id="groupe_sanguin" name="groupe_sanguin" value="<?= htmlspecialchars($user['groupe_sanguin'] ?? ''); ?>" required>

        <label for="date_dernieres_regles">Date de Dernières Règles :</label>
        <input type="date" id="date_dernieres_regles" name="date_dernieres_regles" value="<?= htmlspecialchars($user['date_dernieres_regles'] ?? ''); ?>">

        <label for="date_prevue_accouchement">Date Prévue d'Accouchement :</label>
        <input type="date" id="date_prevue_accouchement" name="date_prevue_accouchement" value="<?= htmlspecialchars($user['date_prevue_accouchement'] ?? ''); ?>">

        <label for="nombre_grossesses_precedentes">Nombre de Grossesses Précédentes :</label>
        <input type="number" id="nombre_grossesses_precedentes" name="nombre_grossesses_precedentes" value="<?= htmlspecialchars($user['nombre_grossesses_precedentes'] ?? ''); ?>">

        <label for="antecedents_medicaux">Antécédents Médicaux :</label>
        <textarea id="antecedents_medicaux" name="antecedents_medicaux"><?= htmlspecialchars($user['antecedents_medicaux'] ?? ''); ?></textarea>

        <label for="allergies">Allergies :</label>
        <textarea id="allergies" name="allergies"><?= htmlspecialchars($user['allergies'] ?? ''); ?></textarea>

        <button type="submit">Mettre à jour</button>
    </form>
</body>
</html>