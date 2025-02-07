<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "clinique_bonheur";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

$email = $_POST['email'];

// Vérifier si l'utilisateur existe
$sql = "SELECT * FROM femmes_enceintes WHERE email='$email'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    $_SESSION['user_id'] = $user['id'];

    // Rediriger vers la page de profil
    header("Location: profil.php");
} else {
    echo "Utilisateur non trouvé.";
}
$conn->close();
?>