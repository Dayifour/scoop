<?php
session_start();
$bd = new PDO('mysql:host=localhost;dbname=base2', 'root');
$user = $bd->query("select * from vente ");
$vente = $bd->query("select * from enregistrement where id = {$_SESSION["id"]}");
$vente = $vente->fetch();
$produits = $bd->query("select * from produit where id_vendeur = {$_SESSION["id"]} ");
$achats =  $bd->query("select * from vente where id_acheteur = {$_SESSION["id"]} ");
if (!isset($_SESSION["contact"])) {
    header('location: index.php');
    exit;
}

if (isset($_GET["id"])) {
    $req = $bd->prepare("INSERT INTO vente VALUES(null, NOW(), ?, ?)");
    $req->execute([$_GET["id"], $_SESSION["id"]]);
    header('location: index.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/publier.css">
    <link rel="stylesheet" href="css/animation.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .list {
            display: flex;
            gap: 40px;
        }

        .produit {
            width: 200px;
        }

        #texte {
            display: flex;
            gap: 40px;
        }

        h2 {
            flex-grow: 1;
            margin: 2px;
        }
    </style>
</head>

<body>
    <div class="titre">
        <header>
            <h1>Mes activités</h1>
            <a href="index.php">Acceuil</a>
        </header>
        <?php
        if (isset($_GET["b"])) { ?>
            <div id="texte">
                <h2> <?= $vente['nom'] ?> <?= $vente['prenom'] ?></h2>

            </div>

            <h2>Les produits vendus</h2>
            <div class="list">
                <?php
                foreach ($produits as $prod) {
                    $vendeur = $bd->query("select * from vente where id_produit ={$prod["id"]} ");
                    $verification1 = 1;
                    foreach ($vendeur as $vend) {
                        $achat = $bd->query("select *  from enregistrement where id = {$vend['id_acheteur']}");
                        $achat = $achat->fetch();
                        $pro_achat = $bd->query("select * from produit where id = {$vend['id_produit']}");
                        $pro_achat = $pro_achat->fetch();
                ?>
                        <div class="produit">
                            <span><img src="<?= $prod['photo'] ?>" width="200" height="186"></span><br>
                            <span><?= $achat['prenom'] ?></span>
                            <span><?= $achat['nom'] ?></span>
                            <span><?= $achat['Contact'] ?></span>
                            <span><?= $pro_achat['nom'] ?></span>
                            <span><?= $pro_achat['marque'] ?></span>
                        </div>

                <?php
                    }
                }
                if (!isset($verification1)) {
                    echo 'Aucun produit vendu!!!!!';
                }
                ?>
            </div>
        <?php

        }
        ?>
    </div>
    <header>
        <a href="#">
            <h1><img src="Logo5.png" alt="logo"></h1>
        </a>
        <div class="bx bx-menu" id="menu-icon"></div>
        <?php if (!isset($_SESSION['contact'])) : ?>
            <form action="index.php" method="post">
                <input type="text" id="contact" name="contact" placeholder="Contact" required>
                <input type="password" id="Mot_de_pass" name="Mot_de_pass" placeholder="Mot de passe " required>
                <button>Se connecter</button>
            </form>
            <a href="user.php">S'inscrire</a><br>
        <?php else : ?>
            <nav>
                <ul class="navbar">
                    <li><a href="index.php#home">Acceuil</a></li>
                    <li><a href="product.php">Publier</a></li>
                    <li><a href="vente.php?b=verification">Activiter</a></li>
                </ul>
            </nav>

        <?php endif; ?>
    </header>

    <script src="script/script.js"></script>
</body>

</html>