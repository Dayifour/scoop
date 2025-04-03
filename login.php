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
  // Vérifier si l'utilisateur existe
  if ($user && password_verify($password, $user['password'])) {
    $_SESSION["id"] = $user["id"];
    $_SESSION["role"] = $user["role"];
    header("Location: index.php");
    exit();
  }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion - Scoop</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    /* ===== VARIABLES ===== */
    :root {
      --primary: #FF6B00;
      --primary-light: #FF8C42;
      --secondary: #2C3E50;
      --light: #F8F9FA;
      --dark: #212529;
      --white: #FFFFFF;
      --gray: #6C757D;
      --border: #E9ECEF;
      --success: #28A745;
      --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
      --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.1);
      --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.12);
      --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
      --border-radius: 10px;
    }

    /* ===== BASE STYLES ===== */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: var(--light);
      color: var(--secondary);
      line-height: 1.6;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    /* ===== HEADER ===== */
    .main-header {
      background-color: var(--white);
      box-shadow: var(--shadow-sm);
      height: 80px;
      display: flex;
      align-items: center;
    }

    .header-container {
      width: 100%;
      max-width: 1200px;
      padding: 0 2rem;
      margin: 0 auto;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .logo {
      height: 100px;
      padding: 10px 0;
      transition: var(--transition);
    }

    .logo:hover {
      transform: scale(1.05);
    }

    .nav-links {
      display: flex;
      gap: 1.75rem;
      list-style: none;
    }

    .nav-links a {
      color: var(--secondary);
      text-decoration: none;
      font-weight: 500;
      font-size: 0.95rem;
      position: relative;
      padding: 0.5rem 0;
      transition: var(--transition);
    }

    .nav-links a:after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      background: var(--primary);
      bottom: 0;
      left: 0;
      transition: var(--transition);
    }

    .nav-links a:hover:after,
    .nav-links a.active:after {
      width: 100%;
    }

    .nav-links a:hover {
      color: var(--primary);
    }

    /* ===== LOGIN SECTION ===== */
    .login-section {
      display: flex;
      justify-content: center;
      align-items: center;
      flex: 1;
      padding: 4rem 2rem;
      background-color: var(--light);
    }

    .login-container {
      max-width: 500px;
      width: 100%;
      background: var(--white);
      border-radius: var(--border-radius);
      box-shadow: var(--shadow-lg);
      overflow: hidden;
      padding: 2.5rem;
      margin: 2rem 0;
    }

    .login-form {
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
    }

    .login-form h2 {
      text-align: center;
      color: var(--primary);
      font-size: 2rem;
      margin-bottom: 0.5rem;
      position: relative;
      padding-bottom: 0.5rem;
    }

    .login-form h2:after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 3px;
      background: var(--primary);
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }

    .form-group label {
      font-weight: 500;
      color: var(--secondary);
    }

    .form-group input {
      padding: 0.75rem 1.25rem;
      border: 1px solid var(--border);
      border-radius: var(--border-radius);
      font-size: 1rem;
      transition: var(--transition);
    }

    .form-group input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.2);
    }

    .login-btn {
      padding: 0.75rem;
      background-color: var(--primary);
      color: var(--white);
      border: none;
      border-radius: var(--border-radius);
      font-weight: 600;
      font-size: 1rem;
      cursor: pointer;
      transition: var(--transition);
      margin-top: 0.5rem;
    }

    .login-btn:hover {
      background-color: var(--primary-light);
      transform: translateY(-2px);
      box-shadow: var(--shadow-md);
    }

    .login-links {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0.75rem;
      margin-top: 1rem;
    }

    .login-links a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 500;
      transition: var(--transition);
    }

    .login-links a:hover {
      text-decoration: underline;
    }

    .error-message {
      color: #dc3545;
      text-align: center;
      font-size: 0.9rem;
      margin-top: -0.5rem;
    }

    /* ===== FOOTER ===== */
    .main-footer {
      background-color: var(--secondary);
      color: var(--white);
      padding: 2rem;
      text-align: center;
      margin-top: auto;
    }

    .footer-content {
      max-width: 1200px;
      margin: 0 auto;
    }

    .footer-contact {
      margin-bottom: 1rem;
    }

    .social-links {
      display: flex;
      justify-content: center;
      gap: 1.5rem;
      margin-top: 1rem;
    }

    .social-links a {
      color: var(--white);
      font-size: 1.25rem;
      transition: var(--transition);
    }

    .social-links a:hover {
      color: var(--primary);
      transform: translateY(-3px);
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
      .login-container {
        padding: 1.5rem;
      }

      .login-form h2 {
        font-size: 1.75rem;
      }
    }

    @media (max-width: 576px) {
      .login-section {
        padding: 2rem 1rem;
      }

      .header-container {
        padding: 0 1rem;
      }
    }
  </style>
</head>

<body>
  <!-- Header -->
  <header class="main-header">
    <div class="header-container">
      <a href="index.php">
        <img src="./image/Logo5.png" alt="Scoop" class="logo">
      </a>
      <nav>
        <ul class="nav-links">
          <li><a href="login.php" class="active">Connexion</a></li>
          <li><a href="user.php">Inscription</a></li>

        </ul>
      </nav>
    </div>
  </header>

  <!-- Login Section -->
  <section class="login-section">
    <div class="login-container">
      <form action="login.php" method="post" class="login-form">
        <h2>Connexion</h2>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="Votre email" required>
        </div>
        <div class="form-group">
          <label for="password">Mot de passe</label>
          <input type="password" id="password" name="password" placeholder="Votre mot de passe" required>
        </div>
        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && (isset($user))) : ?>
          <p class="error-message">Email ou mot de passe incorrect</p>
        <?php endif; ?>
        <button type="submit" class="login-btn">Se connecter</button>
        <div class="login-links">
          <a href="forgot_password.php">Mot de passe oublié ?</a>
          <a href="user.php">Créer un compte</a>
        </div>
      </form>
    </div>
  </section>

  <!-- Footer -->
  <?php include("./components/footer.php") ?>
</body>

</html>