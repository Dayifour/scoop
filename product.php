<?php
session_start();
$bd = new PDO('mysql:host=localhost;dbname=base2', 'root');

if (isset($_POST['produit']) && isset($_FILES['Photo'])) {
    $photo = 'img/' . $_FILES['Photo']['name'];
    move_uploaded_file($_FILES['Photo']['tmp_name'], $photo);

    $req = $bd->prepare("INSERT INTO produit VALUES(null, ?, ?, ?, ?, ?)");
    $req->execute([
        $_POST["produit"],
        $_SESSION["contact"],
        $photo,
        $_POST["prix"],
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
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/publier.css">
    <link rel="stylesheet" href="css/animation.css">
    <style>
header {
    display: flex;
    align-items: center;
    background: hsl(80.78deg 42.56% 62.99%);
    padding: 10px;
}
header a {
    margin-left: 30px;
    color: hsl(174.98deg 59.9% 32.01%);
}
    </style>
</head>

<body>
    <div class="poste">
        <fieldset class="arrangement">
            <h2>Nouvelle publication</h2>
            <p>Remplissez ce formulaire pour publier votre produit</p>
            <form action="product.php" method="POST" enctype="multipart/form-data">
                <p>
                    <label for="name">Nom du produit : </label>
                    <input class="pit" type="text" name="produit" id="produit" minlength="3" maxlength="30" placeholder="Produit" required autofocus><br>
                </p>
                <p>
                    <label for="prixdup">Le prix du produit :</label>
                    <input class="pit" type="number" name="prix" id="prix" placeholder="5000F CFA" required><br>
                </p>
                <p>
                    <label for="pictures">Choisissez une photo du produit :</label>
                    <input type="file" name="Photo" id="Photo" required><br>
                </p>
                <div class="regle">
                    <input type="reset" value="Annuler">
                    <input type="submit" value="Envoyer">
                </div>
            </form>
        </fieldset>
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

    <script>
        document.getElementById("prix").onchange = function() {
            let taux = this.value / 100;
            document.getElementById("taux").textContent = 'taux de 1% = ' + taux;
        }

        document.getElementById("mb").onsubmit = function() {
            let ok = confirm("Acceptez vous de payer le taux ?");
            return ok;
        }
    </script>
    <script src="script/script.js"></script>
</body>

</html>