<?php
session_start();
$bd = new PDO('mysql:host=localhost;dbname=scoopbd', 'root');
// $user = $bd->query("select * from vente ");
$user = $bd->query("select * from users where id = {$_SESSION["id"]}");
$user = $user->fetch();
$produits = $bd->query("select * from produit where id_vendeur = {$_SESSION["id"]} ");
$produits = $produits->fetchAll();
if (!isset($_SESSION["id"])) {
    header('location: index.php');
    exit;
}

// if (isset($_GET["id"])) {
//     $req = $bd->prepare("INSERT INTO vente VALUES(null, NOW(), ?, ?)");
//     $req->execute([$_GET["id"], $_SESSION["id"]]);
//     header('location: index.php');
// }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- <link rel="stylesheet" href="style.css"> -->
    <style>
        .list {
            display: flex;
            gap: 20px;
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

        .err {
            color: red;
            margin-left: 40%;
        }
    </style>
</head>

<body>
    <div class="">
        <header>
            <h1>Mes activités</h1>
            <div>

                <a href="index.php">Acceuil</a>
            </div>
        </header>
        <?php
        if (isset($_GET["b"])) { ?>
            <div id="texte">
                <h3>Bonjour, <?= $user['nom'] ?> <?= $user['prenom'] ?></h3>
            </div>

            <div class="list">
                <?php
                if (!empty($produits)) {
                    echo "<p>Publication</p>";
                    foreach ($produits as $prod) {
                        // $vendeur = $bd->query("select * from vente where id_produit ={$prod["id"]} ");
                        // $verification1 = 1;
                        // foreach ($vendeur as $vend) {
                        // $achat = $bd->query("select *  from users where id = {$vend['id_acheteur']}");
                        // $achat = $achat->fetch();
                        // $pro_achat = $bd->query("select * from produit where id = {$vend['id_produit']}");
                        // $pro_achat = $pro_achat->fetch();
                ?>
                        <div class="produit">
                            <span><img src="uploads/<?= $prod['photo'] ?>" width="200" height="186"></span><br>
                            <span><?= $prod['nom'] ?> : </span>
                            <span><?= $prod['prix'] ?></span>
                        </div>

                <?php
                    }
                    // }
                    // if (!isset($verification1)) {
                    //     echo '<p class="err">Aucun produit vendu!!!!!</p class="err">';
                    // }

                } else {
                    echo '<p class="err">Aucun produit Publier!!!!!</p class="err">';
                }

                ?>
            </div>
        <?php

        }
        ?>
    </div>

    <script src="script/script.js"></script>
</body>

</html>