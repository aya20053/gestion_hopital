<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "clinique_bonheur";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nom = $_POST['nom'];
  $prenom = $_POST['prenom'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

  $sql = "INSERT INTO utilisateurs (nom, prenom, email, password) VALUES (?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ssss", $nom, $prenom, $email, $password);

  if ($stmt->execute()) {
    echo "Inscription réussie!";
  } else {
    echo "Erreur: " . $sql . "<br>" . $conn->error;
  }

  $stmt->close();
}

$conn->close();
?>
