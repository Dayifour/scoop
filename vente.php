<?php
session_start();
if (!isset($_SESSION["id"])) {
    header('location: index.php');
    exit;
}

$bd = new PDO('mysql:host=localhost;dbname=scoopbd', 'root');
$user = $bd->query("SELECT * FROM users WHERE id = {$_SESSION["id"]}");
$user = $user->fetch();
$produits = $bd->query("SELECT * FROM produit WHERE id_vendeur = {$_SESSION["id"]} ");
$produits = $produits->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes activités - Scoop</title>
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

        /* ===== ACTIVITIES SECTION ===== */
        .activities-section {
            flex: 1;
            padding: 3rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .activities-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .activities-title {
            font-size: 2rem;
            color: var(--primary);
            position: relative;
            padding-bottom: 0.5rem;
        }

        .activities-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 80px;
            height: 3px;
            background: var(--primary);
        }

        .user-greeting {
            font-size: 1.1rem;
            color: var(--secondary);
            font-weight: 500;
        }

        .activities-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            border-bottom: 1px solid var(--border);
        }

        .tab-btn {
            padding: 0.75rem 1.5rem;
            background: transparent;
            border: none;
            border-bottom: 3px solid transparent;
            font-weight: 600;
            color: var(--gray);
            cursor: pointer;
            transition: var(--transition);
        }

        .tab-btn.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }

        .tab-btn:hover {
            color: var(--primary);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
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

        .product-price {
            font-weight: 700;
            color: var(--primary);
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .product-actions {
            display: flex;
            gap: 1rem;
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

        .btn-primary {
            background-color: var(--primary);
            color: var(--white);
            border: none;
        }

        .btn-primary:hover {
            background-color: var(--primary-light);
            transform: translateY(-2px);
        }

        .btn-outline {
            background-color: transparent;
            color: var(--primary);
            border: 1px solid var(--primary);
        }

        .btn-outline:hover {
            background-color: rgba(255, 107, 0, 0.1);
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--gray);
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: var(--gray);
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }

        .btn-large {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
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
            body {
                padding-top: 70px;
            }

            .activities-section {
                padding: 2rem 1rem;
            }

            .activities-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .product-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .header-container {
                padding: 0 1rem;
            }

            .activities-tabs {
                overflow-x: auto;
                padding-bottom: 0.5rem;
            }

            .tab-btn {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
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
                    <li><a href="product.php">Publier</a></li>
                    <li><a href="vente.php?b=verification" class="active">Activités</a></li>
                    <li><a href="index.php?a">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Activities Section -->
    <section class="activities-section">
        <div class="activities-header">
            <h1 class="activities-title">Mes activités</h1>
            <p class="user-greeting">Bonjour, <?= $user['nom'] ?> <?= $user['prenom'] ?></p>
        </div>

        <div class="activities-tabs">
            <button class="tab-btn active">Mes publications</button>
            <button class="tab-btn">Ventes</button>
            <button class="tab-btn">Historique</button>
        </div>

        <?php if (!empty($produits)) : ?>
            <div class="products-grid">
                <?php foreach ($produits as $prod) : ?>
                    <div class="product-card">
                        <img src="uploads/<?= $prod['photo'] ?>" alt="<?= $prod['nom'] ?>" class="product-image">
                        <div class="product-info">
                            <h3 class="product-name"><?= $prod['nom'] ?></h3>
                            <p class="product-price"><?= number_format($prod['prix'], 0, ',', ' ') ?> FCFA</p>
                            <div class="product-actions">
                                <a href="product_detail.php?id=<?= $prod['id'] ?>" class="btn btn-outline">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                                <a href="#" class="btn btn-primary">
                                    <i class="fas fa-chart-line"></i> Stats
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <p>Vous n'avez publié aucun produit pour le moment</p>
                <a href="product.php" class="btn btn-primary btn-large">
                    <i class="fas fa-plus"></i> Publier un produit
                </a>
            </div>
        <?php endif; ?>
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
        // Tab functionality can be added here
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                // Here you would add code to switch between content tabs
            });
        });
    </script>
</body>

</html>