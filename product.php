<?php
session_start();
if (!isset($_SESSION["id"])) {
    header('location: login.php');
    exit;
}

$bd = new PDO('mysql:host=localhost;dbname=scoopbd', 'root');

if (isset($_POST['produit']) && isset($_FILES['Photo'])) {
    $photo = 'uploads/' . $_FILES['Photo']['name'];
    move_uploaded_file($_FILES['Photo']['tmp_name'], $photo);

    $req = $bd->prepare("INSERT INTO produit VALUES(null, ?, ?, ?, ?)");
    $req->execute([
        $_POST["produit"],
        $_POST["prix"],
        $_FILES['Photo']['name'],
        $_SESSION["id"]
    ]);

    header('location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publier un produit - Scoop</title>
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
            padding-top: 80px;
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

        /* ===== PUBLICATION SECTION ===== */
        .publication-section {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        .publication-container {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            width: 100%;
            max-width: 800px;
            padding: 2.5rem;
            margin: 2rem 0;
        }

        .publication-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .publication-header h1 {
            color: var(--primary);
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .publication-header p {
            color: var(--gray);
        }

        .publication-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
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

        .form-group input,
        .form-group input[type="file"] {
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

        .form-group input[type="file"] {
            padding: 0.5rem;
        }

        .fee-info {
            background-color: rgba(255, 107, 0, 0.1);
            padding: 1rem;
            border-radius: var(--border-radius);
            text-align: center;
            font-size: 0.9rem;
            color: var(--secondary);
            margin: 1rem 0;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            font-size: 1rem;
        }

        .btn-reset {
            background-color: var(--gray);
            color: var(--white);
        }

        .btn-reset:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }

        .btn-submit {
            background-color: var(--primary);
            color: var(--white);
        }

        .btn-submit:hover {
            background-color: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
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
            .publication-container {
                padding: 1.5rem;
            }

            .publication-header h1 {
                font-size: 1.75rem;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            body {
                padding-top: 70px;
            }

            .header-container {
                padding: 0 1rem;
            }

            .publication-section {
                padding: 1rem;
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
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="product.php" class="active">Publier</a></li>
                    <li><a href="vente.php?b=verification">Activités</a></li>
                    <li><a href="index.php?a">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Publication Section -->
    <section class="publication-section">
        <div class="publication-container">
            <div class="publication-header">
                <h1>Publier un nouveau produit</h1>
                <p>Remplissez ce formulaire pour partager votre produit avec la communauté</p>
            </div>

            <form action="product.php" method="POST" class="publication-form" enctype="multipart/form-data" id="mb">
                <div class="form-group">
                    <label for="produit">Nom du produit</label>
                    <input type="text" name="produit" id="produit" minlength="3" maxlength="30"
                        placeholder="Ex: Chaussures Nike Air Max" required autofocus>
                </div>

                <div class="form-group">
                    <label for="prix">Prix du produit (FCFA)</label>
                    <input type="number" name="prix" id="prix"
                        placeholder="Ex: 15000" required>
                </div>

                <div class="form-group">
                    <label for="Photo">Photo du produit</label>
                    <input type="file" name="Photo" id="Photo" accept="image/*" required>
                </div>

                <div class="fee-info" id="taux">
                    Le taux de 1% sera appliqué à votre produit (0 FCFA pour le moment)
                </div>

                <div class="form-actions">
                    <button type="reset" class="btn btn-reset">Annuler</button>
                    <button type="submit" class="btn btn-submit">Publier le produit</button>
                </div>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-content">
            <p class="footer-contact">Contactez-nous au 92773429 ou 79994640</p>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-whatsapp"></i></a>
            </div>
            <p>&copy; <?= date('Y') ?> Scoop. Tous droits réservés.</p>
        </div>
    </footer>

    <script>
        document.getElementById("prix").addEventListener('input', function() {
            const prix = this.value;
            if (prix > 0) {
                const taux = Math.round(prix * 0.01);
                document.getElementById("taux").innerHTML =
                    `Le taux de 1% sera appliqué à votre produit (${taux} FCFA)`;
            } else {
                document.getElementById("taux").innerHTML =
                    'Le taux de 1% sera appliqué à votre produit (0 FCFA pour le moment)';
            }
        });

        document.getElementById("mb").addEventListener('submit', function(e) {
            const confirmation = confirm("En publiant ce produit, vous acceptez de payer le taux de 1%. Confirmez-vous la publication ?");
            if (!confirmation) {
                e.preventDefault();
            }
        });
    </script>
</body>

</html>