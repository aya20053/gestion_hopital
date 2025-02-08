<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion Femme Enceinte</title>
  <style>
    body {
      font-family: 'Arial', sans-serif;
      margin: 0;
      padding: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background-image: url('cover.jpg'); /* Remplacez par le chemin de votre image */
      background-size: cover;
      background-position: center;
    }

    .login-container {
      background-color: rgba(255, 255, 255, 0.4); /* Transparence */
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
      backdrop-filter: blur(5px); /* Effet de flou */
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
  </style>
</head>
<body>
  <div class="login-container">
    <form action="dashboard.php" method="POST">
      <label>Email :</label>
      <input type="email" name="email" required>

      <label>Mot de passe :</label>
      <input type="password" name="password" required>

      <button type="submit">Se connecter</button>
    </form>
  </div>
</body>
</html>