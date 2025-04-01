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
} catch (error) {
    echo "erreur de connexion veuille reessayer plus tard";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>

    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/publier.css">
    <link rel="stylesheet" href="./css/animation.css">
    <style>
        .recherche {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        #search {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f5f5f5;
            padding: 10px;
            border-radius: 8px;
            width: fit-content;
        }

        #search input[type="text"] {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            outline: none;
            transition: border-color 0.3s;
        }

        #search input[type="text"]:focus {
            border-color: darkorange;
        }

        #search input[type="submit"] {
            padding: 8px 12px;
            background: darkorange;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        #search input[type="submit"]:hover {
            background: #0056b3;
        }

        form {
            background: transparent;
            border: 0;
        }

        #search {
            margin: 0;
        }

        #list {
            text-align: center;
        }


        h2 {
            flex-grow: 1;
            margin: 0;
        }

        .old_prix {
            text-decoration: line-through;
        }
    </style>
</head>

<body>
    <header>
        <a href="#" style="flex: 1;">
            <h1><img class="logo" src="./image/Logo5.png" alt="logo"></h1>
        </a>
        <nav>
            <ul class="navbar">
                <?php if (!isset($_SESSION["id"])) : ?>

                    <li><a href="./login.php">Se connecter</a></li>
                    <li><a href="user.php">S'inscrire</a></li>
                <?php else : ?>

                    <li><a href="index.php#home">Acceuil</a></li>
                    <li><a href="product.php">Publier</a></li>
                    <li><a href="vente.php?b=verification">Activiter</a></li>
                    <li><a href="index.php?a">deconnexion</a></li>
            </ul>
        </nav>
        <div class="bx bx-menu" id="menu-icon"></div>
    <?php endif; ?>
    </header>

    <section class="home" id="home">
        <div class="home1">
            <h2 class="homeh2">Du look</h2>
            <p class="homep">Ici, vous retrouverez vos chaussures de rêves aux prix exeptionnels</p>
            <p><a href="https://wa.me/22374815107?text=mohamed!">Envoyez-moi un e-mail!</a></p>
        </div>
        <img id="homeimg" src="./image/R.JPG" alt="image-d'acceuil">
    </section>
    <h2 id="bouge" class="tag">Nos produits</h2>
    <div class="recherche">
        <form id="search" method="GET" action="index.php?search">
            <input type="text" name="search" class="search" placeholder="recherche">
            <input type="submit" value="search">
        </form>
    </div>
    <div id="list">
        <div class="article">
            <?php foreach ($produit as $prod) { ?>
                <div class="row produits">
                    <img src="./uploads/<?= $prod['photo'] ?>"> <br>
                    <?= $prod['nom'] ?>
                    <div class="prix_container">
                        <span class="barree"><?= $prod['prix'] - ($prod['prix'] / 20) ?>FCFA</span>
                        <span class="prix"><?= $prod['prix'] ?> F CFA</span>
                    </div>

                    <?php
                    $achats = $bd->query("select *  from users where id = '" . $prod['id_vendeur'] . "'");
                    $achat = $achats->fetch();
                    echo $achat['contact'];
                    ?>
                    <div class="achat">
                        <a class="achatbuton" href="https://wa.me/223<?= $achat['contact'] ?>?text=Je suis intéressé par votre chaussure '<?= $prod['nom'] ?>'" ?>Commander</a>
                    </div>
                </div>
            <?php } ?>
        </div>

    </div>

    <!-- Pied de page -->
    <footer>
        <p>&copy; Contactez-nous au 92773429 ou 79994640</p>
        <div class="social-media">
            <p><i class="fab fa-facebook-f"></i></p>
            <p><i class="fab fa-twitter"></i></p>
            <p><i class="fab fa-instagram"></i></p>
            <p><i class="fab fa-linkedin-in"></i></p>
        </div>
    </footer>
    <!-- Fin du pied de page -->
    <script src="script/script.js"></script>
</body>

</html>