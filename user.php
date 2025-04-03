<?php
session_start();
$bd = new PDO('mysql:host=localhost;dbname=scoopbd', 'root');
$users = $bd->query('select * from users');

if (isset($_POST['nom'])) {


    // Vérification de l'existence de l'email ou du contact
    $stmt = $bd->prepare("SELECT * FROM users WHERE email = :email OR contact = :contact");
    $stmt->execute([
        ':email' => $_POST["email"],
        ':contact' => $_POST["contact"]
    ]);
    if ($stmt->rowCount() > 0) {
        echo "<script>alert('Email ou numéro de téléphone déjà utilisé.');</script>";
    } else {
        $hashedPassword = password_hash($_POST["password"], PASSWORD_DEFAULT);

        $bd->exec("INSERT INTO users VALUES ('" . null . "',
            '" . $_POST["nom"] . "',
            '" . $_POST["prenom"] . "', 
            '" . $_POST["contact"] . "',
            '" . "Client" . "',
            '" . $_POST["email"] . "',
            '" . $hashedPassword . "')");
        header('location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Scoop</title>
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
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.12);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
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
            padding-top: 80px;
            /* Space for fixed header */
        }

        /* ===== HEADER ===== */
        .main-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background-color: var(--white);
            box-shadow: var(--shadow-sm);
            z-index: 1000;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .header-container {
            width: 100%;
            max-width: 1200px;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            padding: 10px 0;
            height: 100px;
            transition: var(--transition);
        }

        .logo:hover {
            opacity: 0.9;
        }

        .nav-links {
            display: flex;
            gap: 1.5rem;
            list-style: none;
        }

        .nav-links a {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            position: relative;
            padding: 0.5rem 0;
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

        /* ===== MAIN CONTENT ===== */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 2rem;
        }

        .auth-container {
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
        }

        .auth-card {
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            transition: var(--transition);
        }

        .auth-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .auth-header {
            padding: 1.5rem 2rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
            text-align: center;
        }

        .auth-header h2 {
            font-weight: 600;
            font-size: 1.5rem;
        }

        .auth-body {
            padding: 2rem;
        }

        /* ===== FORM ELEMENTS ===== */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--secondary);
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 0.95rem;
            transition: var(--transition);
            background-color: var(--light);
        }

        .form-control:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.2);
            background-color: var(--white);
        }

        .btn {
            display: block;
            width: 100%;
            padding: 0.875rem;
            background-color: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
            margin-top: 1.5rem;
        }

        .btn:hover {
            background-color: var(--primary-light);
            transform: translateY(-2px);
        }

        .form-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--gray);
            font-size: 0.9rem;
        }

        .form-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        /* ===== FOOTER ===== */
        .main-footer {
            background-color: var(--secondary);
            color: var(--white);
            padding: 1.5rem;
            text-align: center;
            font-size: 0.9rem;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            body {
                padding-top: 70px;
            }

            .main-header {
                height: 70px;
            }

            .header-container {
                padding: 0 1rem;
            }

            .nav-links {
                gap: 1rem;
            }

            .auth-body {
                padding: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .main-content {
                padding: 1.5rem;
            }

            .auth-header {
                padding: 1.25rem 1.5rem;
            }
        }
    </style>
</head>

<body>
    <!-- Fixed Header -->
    <header class="main-header">
        <div class="header-container">
            <a href="index.php">
                <img src="./image/Logo5.png" alt="Scoop" class="logo">
            </a>
            <nav>
                <ul class="nav-links">
                    <?php if (!isset($_SESSION["id"])) : ?>
                        <li><a href="login.php">Connexion</a></li>
                        <li><a href="user.php" class="active">Inscription</a></li>
                    <?php else : ?>
                        <li><a href="index.php">Accueil</a></li>
                        <li><a href="product.php">Publier</a></li>
                        <li><a href="vente.php">Activités</a></li>
                        <li><a href="index.php?a">Déconnexion</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-header">
                    <h2>Créer un compte</h2>
                </div>
                <div class="auth-body">
                    <form action="user.php" method="post">
                        <div class="form-group">
                            <label for="prenom" class="form-label">Prénom</label>
                            <input type="text" id="prenom" name="prenom" class="form-control" placeholder="Entrez votre prénom" required>
                        </div>

                        <div class="form-group">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" id="nom" name="nom" class="form-control" placeholder="Entrez votre nom" required>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Adresse email</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="exemple@email.com" required>
                        </div>

                        <div class="form-group">
                            <label for="contact" class="form-label">Numéro de téléphone</label>
                            <input type="tel" id="contact" name="contact" class="form-control" placeholder="77 123 45 67" required>
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Créez un mot de passe sécurisé" required>
                        </div>

                        <button type="submit" class="btn">S'inscrire</button>

                        <div class="form-footer">
                            Vous avez déjà un compte ? <a href="login.php">Connectez-vous</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include("./components/footer.php") ?>
</body>

</html>