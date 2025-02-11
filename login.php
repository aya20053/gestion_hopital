<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion</title>
  <style>
    body {
      font-family: 'Arial', sans-serif;
      margin: 0;
      padding: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background-image: url('l.jpg'); /* Remplacez par le chemin de votre image */
      background-size: cover;
      background-position: center;
    }

    .login-container {
      background-color: rgba(255, 255, 255, 0.8); /* Transparence améliorée */
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
      backdrop-filter: blur(5px); /* Effet de flou */
    }

    h2 {
      text-align: center;
      font-size: 30px;
      margin-bottom: 20px;
    }

    .login-container label {
      display: block;
      margin-bottom: 8px;
      color: #555;
      font-size: 14px;
    }

    .login-container input[type="email"],
    .login-container input[type="password"] {
      width: 100%;
      padding: 12px;
      margin-bottom: 20px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 14px;
      box-sizing: border-box;
    }

    .login-container input:focus {
      border-color: #35b4c6;
      outline: none;
    }

    .login-container button {
      width: 100%;
      padding: 12px;
      background-color: #35b4c6;
      color: white;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .login-container button:hover {
      background-color: #0056b3;
    }

    .login-container p {
      text-align: center;
      margin-top: 10px;
    }

    .login-container a {
      color: #35b4c6;
      text-decoration: none;
    }

    .login-container a:hover {
      text-decoration: underline;
    }

    /* Style pour le bouton de retour */
    .back-button {
      display: flex;
      justify-content: flex-start; /* Aligner le contenu à gauche */
      margin-top: 20px;
    }

    .back-button a {
      display: flex;
      align-items: center; /* Centrer l'icône verticalement */
      padding: 10px 20px;
      background-color: #35b4c6;
      color: white;
      border-radius: 5px;
      text-decoration: none;
      transition: background-color 0.3s ease;
    }

    .back-button a:hover {
      background-color: #0056b3;
    }

    .back-button i {
      margin-right: 8px; /* Espace entre l'icône et le texte */
    }
  </style>

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body>
  <div class="login-container">
    <form action="dashboard.php" method="POST">
      <h2><i class="fas fa-sign-in-alt"></i> Se connecter</h2>

      <label><i class="fas fa-envelope"></i> Email :</label>
      <input type="email" name="email" required>

      <label><i class="fas fa-lock"></i> Mot de passe :</label>
      <input type="password" name="password" required>

      <button type="submit"><i class="fas fa-sign-in-alt"></i> Se connecter</button>

      <p>Vous n'avez pas de compte ? <a href="inscription.html">Inscrivez-vous</a></p>
    </form>

    <!-- Bouton de retour -->
    <div class="back-button">
      <a href="index.html"><i class="fas fa-arrow-left"></i> Retour à l'accueil</a>
    </div>
  </div>
</body>

</html>