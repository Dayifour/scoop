<?php
session_start();
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
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/publier.css">
    <link rel="stylesheet" href="css/animation.css">
</head>

<body>
    <header>
        <h1>Nouveau Produits</h1>
        <div>

            <a href="index.php">Acceuil</a>
        </div>
    </header>
    <div class="poste">
        <fieldset class="arrangement">
            <h2>Nouvelle publication</h2>
            <p>Remplissez ce formulaire pour publier votre produit</p>
            <form action="product.php" method="POST" id="mb" enctype="multipart/form-data">
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
                <div id="taux"></div>
                <div class="regle">
                    <input type="reset" value="Annuler">
                    <input type="submit" value="Envoyer">
                </div>
            </form>
        </fieldset>
    </div>

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