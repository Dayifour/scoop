<?php
session_start();

// Vérifier les permissions
if (!isset($_SESSION["id"]) || $_SESSION["role"] !== 'Admin') {
  header('Location: index.php');
  exit;
}

// Connexion directe à la base de données
$bd = new PDO('mysql:host=localhost;dbname=scoopbd', 'root', '');
$bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Récupérer l'utilisateur à modifier
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user = [];

if ($user_id) {
  $stmt = $bd->prepare("SELECT * FROM users WHERE id = ?");
  $stmt->execute([$user_id]);
  $user = $stmt->fetch();

  if (!$user) {
    header('Location: vente.php?error=user_not_found');
    exit;
  }
}

// Gestion de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nom = htmlspecialchars($_POST['nom']);
  $prenom = htmlspecialchars($_POST['prenom']);
  $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
  $contact = preg_replace('/[^0-9]/', '', $_POST['contact']);

  try {
    // Mise à jour de l'utilisateur
    $stmt = $bd->prepare("UPDATE users 
                            SET nom = ?, prenom = ?, email = ?, contact = ?
                            WHERE id = ?");
    $stmt->execute([$nom, $prenom, $email, $contact, $user_id]);

    // Journaliser l'action
    $admin_id = $_SESSION["id"];
    $description = "Modification de l'utilisateur ID $user_id";
    $stmt = $bd->prepare("INSERT INTO admin_logs (admin_id, action_type, target_id, description)
                            VALUES (?, 'update_user', ?, ?)");
    $stmt->execute([$admin_id, $user_id, $description]);

    header("Location: vente.php?success=user_updated");
    exit;
  } catch (PDOException $e) {
    die("Erreur de mise à jour : " . $e->getMessage());
  }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Modifier utilisateur - Scoop</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #FF6B00;
      --primary-light: #FF8C42;
      --secondary: #2C3E50;
      --light: #F8F9FA;
      --white: #FFFFFF;
      --gray: #6C757D;
      --border: #E9ECEF;
      --success: #28A745;
      --danger: #DC3545;
      --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
      --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.1);
      --border-radius: 8px;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: var(--light);
      color: var(--secondary);
      line-height: 1.6;
      margin: 0;
      padding: 0;
    }

    .edit-container {
      max-width: 600px;
      margin: 2rem auto;
      padding: 2rem;
      background: var(--white);
      border-radius: var(--border-radius);
      box-shadow: var(--shadow-md);
    }

    h1 {
      color: var(--primary);
      margin-bottom: 2rem;
      font-size: 1.8rem;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: var(--secondary);
    }

    input[type="text"],
    input[type="email"],
    input[type="tel"] {
      width: 100%;
      padding: 0.8rem;
      border: 1px solid var(--border);
      border-radius: var(--border-radius);
      font-family: 'Poppins', sans-serif;
      transition: all 0.3s ease;
    }

    input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 2px rgba(255, 107, 0, 0.2);
    }

    .form-actions {
      margin-top: 2rem;
      display: flex;
      gap: 1rem;
      justify-content: flex-end;
    }

    .btn {
      padding: 0.6rem 1.2rem;
      border-radius: var(--border-radius);
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      font-size: 0.9rem;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      border: none;
    }

    .btn-outline {
      background-color: transparent;
      color: var(--primary);
      border: 1px solid var(--primary);
    }

    .btn-outline:hover {
      background-color: rgba(255, 107, 0, 0.1);
    }

    .btn-primary {
      background-color: var(--primary);
      color: var(--white);
    }

    .btn-primary:hover {
      background-color: var(--primary-light);
      transform: translateY(-2px);
    }

    @media (max-width: 768px) {
      .edit-container {
        margin: 1rem;
        padding: 1.5rem;
      }

      .form-actions {
        flex-direction: column;
      }

      .btn {
        width: 100%;
        justify-content: center;
      }
    }
  </style>
</head>

<body>
  <!-- Vous pouvez inclure votre header ici si nécessaire -->
  <!-- <?php include 'includes/header.php'; ?> -->

  <main class="edit-container">
    <h1><i class="fas fa-user-edit"></i> Modifier l'utilisateur</h1>

    <form method="POST">
      <div class="form-group">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($user['nom'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($user['prenom'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label for="email">Email :</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label for="contact">Téléphone :</label>
        <input type="tel" id="contact" name="contact" value="<?= htmlspecialchars($user['contact'] ?? '') ?>">
      </div>

      <div class="form-actions">
        <a href="vente.php" class="btn btn-outline">
          <i class="fas fa-times"></i> Annuler
        </a>
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> Enregistrer
        </button>
      </div>
    </form>
  </main>

  <!-- Vous pouvez inclure votre footer ici si nécessaire -->
  <!-- <?php include 'includes/footer.php'; ?> -->
</body>

</html>