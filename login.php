<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = $_POST['email'];
  $password = $_POST['password'];
  // Connexion à la base de données
  $host = 'localhost';
  $db = 'scoopbd';
  $user = 'root';
  $dbpassword = '';

  $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

  $pdo = new PDO($dsn, $user, $dbpassword);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Préparer et exécuter la requête


  $stmt = $pdo->prepare("SELECT * FROM `users` WHERE email =?");
  $stmt->execute([$email]);
  $user = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="./css/login.css">
</head>

<body>
  <div class="login-container">
    <form action="login.php" method="post" class="login-form">
      <h2>Se connecter</h2>
      <div class="form-group">
        <label for="contact">Email</label>
        <input type="text" id="email" name="email" placeholder="email" required>
      </div>
      <div class="form-group">
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" placeholder="Mot de passe" required>
      </div>
      <?php
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Vérifier si l'utilisateur existe
        if ($user && password_verify($password, $user['password'])) {
          $_SESSION["id"] = $user["id"];
          $_SESSION["role"] = $user["role"];
          header("Location: index.php");
          exit();
        } else {
          echo "<p style='color:red;'>Email ou mot de passe incorrect</p>";
        }
      }
      ?>
      <button type="submit">Se connecter</button>
      <p><a href="forgot_password.php" style="text-decoration: none; color: blue;">Mot de passe oublié ?</a></p>
    </form>
  </div>
</body>

</html>