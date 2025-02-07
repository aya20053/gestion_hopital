<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
<form action="maj_profil.php" method="POST">
    <label>Date de Naissance :</label>
    <input type="date" name="date_naissance" required>

    <label>Téléphone :</label>
    <input type="text" name="telephone" required>

    <label>Adresse :</label>
    <textarea name="adresse" required></textarea>

    <label>Groupe Sanguin :</label>
    <input type="text" name="groupe_sanguin" required>

    <button type="submit">Mettre à jour</button>
</form>

</body>
</html>