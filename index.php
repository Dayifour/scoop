<?php
session_start();

try {
    $bd = new PDO('mysql:host=localhost;dbname=scoopbd', 'root');
    if (isset($_GET["search"])) {
        $produit = $bd->query("select * from produit where nom like '%{$_GET["search"]}%'");
    } else {
        $produit = $bd->query('select * from produit');
    }
    if (isset($_GET['a'])) {
        unset($_SESSION["id"]);
        unset($_SESSION["role"]);
    }
} catch (error $e) {
    echo "erreur de connexion veuille reessayer plus tard";
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Scoop</title>
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

        /* ===== HERO SECTION ===== */
        .hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 4rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .hero-content {
            flex: 1;
            padding-right: 2rem;
        }

        .hero-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .hero-text {
            font-size: 1.1rem;
            color: var(--secondary);
            margin-bottom: 2rem;
            max-width: 500px;
        }

        .hero-btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: var(--primary);
            color: var(--white);
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-outline {
            margin-bottom: 4px;
            background-color: transparent;
            color: var(--primary);
            border: 1px solid var(--primary);
        }

        .btn-outline:hover {
            background-color: rgba(255, 107, 0, 0.1);
        }




        .hero-btn:hover {
            background-color: var(--primary-light);
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .hero-image {
            flex: 1;
            text-align: center;
        }

        .hero-image img {
            max-width: 100%;
            height: auto;
            border-radius: var(--border-radius);

        }

        /* ===== PRODUCTS SECTION ===== */
        .products-section {
            padding: 3rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .section-title {
            text-align: center;
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 2rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--primary);
        }

        .search-container {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .search-form {
            display: flex;
            background: var(--white);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            width: 100%;
            max-width: 500px;
        }

        .search-input {
            flex: 1;
            padding: 0.75rem 1.25rem;
            border: none;
            font-size: 1rem;
            outline: none;
        }

        .search-btn {
            padding: 0 1.5rem;
            background-color: var(--primary);
            color: var(--white);
            border: none;
            cursor: pointer;
            transition: var(--transition);
        }

        .search-btn:hover {
            background-color: var(--primary-light);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .product-card {
            background: var(--white);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .product-info {
            padding: 1.5rem;
        }

        .product-name {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--secondary);
        }

        .price-container {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .old-price {
            text-decoration: line-through;
            color: var(--gray);
            font-size: 0.9rem;
        }

        .current-price {
            font-weight: 700;
            color: var(--primary);
            font-size: 1.2rem;
        }

        .seller-info {
            font-size: 0.9rem;
            color: var(--gray);
            margin-bottom: 1rem;
        }

        .order-btn {
            display: block;
            width: 100%;
            padding: 0.75rem;
            background-color: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
            text-decoration: none;
        }

        .order-btn:hover {
            background-color: var(--primary-light);
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
            .hero {
                flex-direction: column;
                text-align: center;
                padding: 3rem 2rem;
            }

            .hero-content {
                padding-right: 0;
                margin-bottom: 2rem;
            }

            .hero-text {
                margin-left: auto;
                margin-right: auto;
            }
        }

        @media (max-width: 768px) {
            body {
                padding-top: 70px;
            }

            .header-container {
                padding: 0 1.5rem;
            }

            .nav-links {
                gap: 1rem;
            }

            .hero-title {
                font-size: 2rem;
            }
        }

        @media (max-width: 576px) {
            .header-container {
                padding: 0 1rem;
            }

            .hero {
                padding: 2rem 1rem;
            }

            .hero-title {
                font-size: 1.75rem;
            }

            .products-grid {
                grid-template-columns: 1fr;
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
                        <li><a href="user.php">Inscription</a></li>
                    <?php else : ?>
                        <li><a href="index.php" class="active">Accueil</a></li>
                        <li><a href="product.php">Publier</a></li>
                        <li><a href="vente.php">Activités</a></li>
                        <li><a href="index.php?a">Déconnexion</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-content">
            <h1 class="hero-title">Du look exceptionnel</h1>
            <p class="hero-text">Découvrez vos chaussures de rêve à des prix imbattables. Qualité, style et confort réunis.</p>
            <a href="https://wa.me/22374815107?text=Bonjour!" class="hero-btn">Contactez-nous</a>
        </div>
        <div class="hero-image">
            <img src="./image/R.png" alt="Collection de chaussures">
        </div>
    </section>

    <!-- Products Section -->
    <section class="products-section">
        <h2 class="section-title">Nos produits</h2>

        <div class="search-container">
            <form class="search-form" method="GET" action="index.php?search">
                <input type="text" name="search" class="search-input" placeholder="Rechercher un produit...">
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <div class="products-grid">
            <?php foreach ($produit as $prod) {
                $achats = $bd->query("select * from users where id = '" . $prod['id_vendeur'] . "'");
                $achat = $achats->fetch();
            ?>
                <div class="product-card">
                    <img src="uploads/<?= $prod['photo'] ?>" alt="<?= $prod['nom'] ?>" class="product-image">
                    <div class="product-info">
                        <h3 class="product-name"><?= $prod['nom'] ?></h3>
                        <p class="product-price"><?= number_format($prod['prix'], 0, ',', ' ') ?> FCFA</p>
                        <div class="product-actions">
                            <a href="product_detail.php?id=<?= $prod['id'] ?>" class="btn btn-outline">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                            <?php if (!empty($achat['contact']) && !empty($prod['nom'])): ?>
                                <a href="https://wa.me/223<?= htmlspecialchars($achat['contact']) ?>?text=Je suis intéressé par votre chaussure '<?= urlencode($prod['nom']) ?>'" class="order-btn">
                                    Commander
                                </a>
                            <?php else: ?>
                                <p>Informations manquantes pour la commande.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            <?php } ?>
        </div>
    </section>

    <!-- Footer -->
    <?php include("./components/footer.php") ?>

    <script src="script/script.js"></script>
</body>

</html>