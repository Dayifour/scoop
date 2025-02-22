<?php
session_start();
$bd = new PDO('mysql:host=localhost;dbname=scoopbd', 'root');
if (isset($_GET["search"])) {
    $produit = $bd->query("select * from produit where nom like '%{$_GET["search"]}%'");
} else {
    $produit = $bd->query('select * from produit');
}
if (isset($_POST['contact'])) {
    $users = $bd->prepare('select * from users where contact=? && pass=?');
    $user = $users->execute([$_POST["contact"], $_POST["Mot_de_pass"]]);
    if ($user) {
        $_SESSION["contact"] = $_POST["contact"];
        $_SESSION["pass"] = $_POST["Mot_de_pass"];
    }
} else if (isset($_GET['a'])) {
    unset($_SESSION["id"]);
    unset($_SESSION["contact"]);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/publier.css">
    <link rel="stylesheet" href="css/animation.css">
    <link rel="stylesheet" href="style.css">
    <style>
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

    <div class="titre">
        <form id="search" method="GET" action="index.php">
            <input type="text" name="search" class="search" placeholder="recherche">
            <input type="submit" value="search">
        </form>
    </div>
    <section class="home" id="home">
        <div class="home1">
            <h2 class="homeh2">Du look</h2>
            <p class="homep">Ici, vous retrouverez vos chaussures de rêves aux prix exeptionnels</p>
            <p><a href="https://wa.me/22374815107?text=mohamed!">Envoyez-moi un e-mail!</a></p>
        </div>
        <img id="homeimg" src="acc.jpg" alt="image-d'acceuil">
    </section>
    <h2 id="bouge">Nos produits</h2>
    <div id="list">
        <div class="article">
            <?php foreach ($produit as $prod) { ?>
                <div class="row">

                    <img src="<?= $prod['photo'] ?>" width="200" height="186"> <br>
                    <?= $prod['nom'] ?>
                    <span class="barré">
                        <div class="old_prix"><?= $prod['prix'] + 2000 ?>FCFA </div>
                    </span><span class="prix"><?= $prod['prix'] ?></span>
                    <?php
                    $achats = $bd->query("select *  from users where id = {$prod['id_vendeur']}");
                    $achat = $achats->fetch();
                    ?>
                    <div class="achat">
                        <a class="achatbuton" href="https://wa.me/223<?= $achat['contact'] ?>?text=Salut!" ?>Commander</a>
                    </div>
                </div>
            <?php } ?>
        </div>

    </div>
    <header>
        <a href="#" style="flex: 1;">
            <h1><img src="Logo5.png" alt="logo"></h1>
        </a>
        <?php if (!isset($_SESSION['contact'])) : ?>
            <a href="/login">Se connecter</a>
            <a href="user.php">S'inscrire</a><br>
        <?php else : ?>

            <div class="bx bx-menu" id="menu-icon"></div>
            <nav>
                <ul class="navbar">
                    <li><a href="index.php#home">Acceuil</a></li>
                    <li><a href="product.php">Publier</a></li>
                    <li><a href="vente.php?b=verification">Activiter</a></li>
                </ul>
            </nav>

        <?php endif; ?>
    </header>
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