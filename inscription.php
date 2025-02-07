<?php
// Connexion à la base de données
$host = "localhost"; // Adresse du serveur MySQL
$user = "root"; // Nom d'utilisateur MySQL (par défaut "root" sous XAMPP)
$pass = ""; // Mot de passe MySQL (vide par défaut sous XAMPP)
$dbname = "clinique_bonheur"; // Nom de la base de données

$conn = new mysqli($host, $user, $pass, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Vérifier que les champs obligatoires ne sont pas vides
    if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
        echo "Tous les champs sont obligatoires.";
        exit();
    }

    // Vérifier si l'email est déjà utilisé
    $sql_check = "SELECT id FROM femmes_enceintes WHERE email = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo "Cet email est déjà utilisé. Veuillez en choisir un autre.";
        exit();
    }
    $stmt_check->close();

    // Hasher le mot de passe
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Insérer les données dans la base de données
    $sql = "INSERT INTO femmes_enceintes (nom, prenom, email, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nom, $prenom, $email, $password_hash);

    if ($stmt->execute()) {
        echo "Inscription réussie ! <a href='login.php'>Connectez-vous</a>";
    } else {
        echo "Erreur lors de l'inscription : " . $conn->error;
    }

    $stmt->close();
}

// Fermer la connexion
$conn->close();
?>
