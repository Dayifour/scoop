<?php
session_start();
$bd = new PDO('mysql:host=localhost;dbname=base2', 'root');
if(isset($_GET['id'])){
    $produit = $bd->query("select * from produit where id={$_GET['id']}");
    $prod = $produit->fetch();
    $achats = $bd->query("select *  from enregistrement where id = {$prod['id_vendeur']}");
    $achat = $achats->fetch();
} else {
    header('location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du produit</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/publier.css">
    <link rel="stylesheet" href="css/animation.css">
    <link rel="stylesheet" href="style.css">
</head>
<body> 
    
<div class="titre">
    <h1><?= $prod['nom'] ?> <?= $prod['marque'] ?></h1>
    <div>
        <img src="<?= $prod['photo'] ?>" ><br>
        <a href="https://wa.me/223<?= $achat['Contact'] ?>?text=Salut!">Commander</a>
    </div>
    <header >
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
            <a href="user.php" align="right">S'inscrire</a><br>
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
</body>
</html>