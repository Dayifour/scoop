<?php
session_start();
$bd = new PDO('mysql:host=localhost;dbname=base2', 'root');
if (isset($_GET['id'])) {
    $produit = $bd->query("select * from produit where id={$_GET['id']}");
    $prod = $produit->fetch();
    $achats = $bd->query("select * from enregistrement where id = {$prod['id_vendeur']}");
    $achat = $achats->fetch();
} else {
    header('location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $prod['nom'] ?> - Détails du produit | Scoop</title>
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

        /* ===== PRODUCT DETAIL SECTION ===== */
        .product-detail-section {
            flex: 1;
            padding: 3rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .product-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            padding: 2.5rem;
        }

        .product-images {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .main-image {
            width: 100%;
            height: 400px;
            object-fit: contain;
            border-radius: var(--border-radius);
            background: var(--light);
            padding: 1rem;
        }

        .product-info {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .product-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--secondary);
        }

        .product-meta {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .product-price {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary);
        }

        .product-seller {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .seller-label {
            font-weight: 500;
        }

        .seller-contact {
            color: var(--primary);
            font-weight: 600;
        }

        .product-description {
            margin-top: 1.5rem;
            color: var(--gray);
            line-height: 1.7;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background-color: var(--primary);
            color: var(--white);
            border: none;
        }

        .btn-primary:hover {
            background-color: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-outline {
            background-color: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-outline:hover {
            background-color: rgba(255, 107, 0, 0.1);
            transform: translateY(-2px);
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
        @media (max-width: 992px) {
            .product-container {
                grid-template-columns: 1fr;
            }

            .main-image {
                height: 300px;
            }
        }

        @media (max-width: 768px) {
            body {
                padding-top: 70px;
            }

            .product-detail-section {
                padding: 2rem 1rem;
            }

            .product-container {
                padding: 1.5rem;
            }

            .product-title {
                font-size: 1.75rem;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .header-container {
                padding: 0 1rem;
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
                    <?php if (!isset($_SESSION['contact'])) : ?>
                        <li><a href="login.php">Connexion</a></li>
                        <li><a href="user.php">Inscription</a></li>
                    <?php else : ?>
                        <li><a href="index.php">Accueil</a></li>
                        <li><a href="product.php">Publier</a></li>
                        <li><a href="vente.php?b=verification">Activités</a></li>
                        <li><a href="index.php?a">Déconnexion</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Product Detail Section -->
    <section class="product-detail-section">
        <div class="product-container">
            <div class="product-images">
                <img src="<?= $prod['photo'] ?>" alt="<?= $prod['nom'] ?>" class="main-image">
            </div>

            <div class="product-info">
                <h1 class="product-title"><?= $prod['nom'] ?> <?= isset($prod['marque']) ? $prod['marque'] : '' ?></h1>

                <div class="product-meta">
                    <span class="product-price"><?= number_format($prod['prix'], 0, ',', ' ') ?> FCFA</span>

                    <div class="product-seller">
                        <span class="seller-label">Vendeur:</span>
                        <span class="seller-contact"><?= $achat['Contact'] ?></span>
                    </div>
                </div>

                <?php if (isset($prod['description']) && !empty($prod['description'])) : ?>
                    <div class="product-description">
                        <h3>Description</h3>
                        <p><?= $prod['description'] ?></p>
                    </div>
                <?php endif; ?>

                <div class="action-buttons">
                    <a href="https://wa.me/223<?= $achat['Contact'] ?>?text=Je suis intéressé par votre produit '<?= $prod['nom'] ?>'"
                        class="btn btn-primary">
                        <i class="fab fa-whatsapp"></i> Commander via WhatsApp
                    </a>
                    <a href="index.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Retour aux produits
                    </a>
                </div>
            </div>
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
</body>

</html>