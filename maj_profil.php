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

$user_id = $_SESSION['user_id'];
$date_naissance = $_POST['date_naissance'];
$telephone = $_POST['telephone'];
$adresse = $_POST['adresse'];
$groupe_sanguin = $_POST['groupe_sanguin'];

// Mettre à jour la base de données
$sql = "UPDATE femmes_enceintes SET 
    date_naissance='$date_naissance', 
    telephone='$telephone', 
    adresse='$adresse', 
    groupe_sanguin='$groupe_sanguin',
    profil_complet=1 
    WHERE id=$user_id";

if ($conn->query($sql) === TRUE) {
    $_SESSION['profil_complet'] = 1; // Mise à jour de la session
    echo "Profil mis à jour avec succès. <a href='profil.php'>Retour au tableau de bord</a>";
} else {
    echo "Erreur : " . $conn->error;
}

$conn->close();
?>
