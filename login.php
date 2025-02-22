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
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Connexion à la base de données
        $conn = new mysqli('localhost', 'root', '', 'scoopbd');

        // Vérifier la connexion
        if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
        }

        // Préparer et exécuter la requête
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        // Vérifier si l'utilisateur existe
        if ($result->num_rows > 0) {
          header("Location: index.php");
          exit();
        } else {
          echo "<p style='color:red;'>Email ou mot de passe incorrect</p>";
        }

        // Fermer la connexion
        $stmt->close();
        $conn->close();
      }
      ?>
      <button type="submit">Se connecter</button>
    </form>
  </div>
</body>

</html>