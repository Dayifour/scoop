<?php
session_start();
$bd = new PDO('mysql:host=localhost;dbname=base2', 'root');
$users = $bd->query('select * from enregistrement');

if (isset($_POST['lastname'])) {
    foreach ($users as $user) {
        if ($user["Contact"] == $_POST["contact"]) {
            $_SESSION["contact"] = $_POST["contact"];
            header('location: index.php');
            exit;
        }
    }

    $bd->exec("INSERT INTO enregistrement VALUES ('" . null . "',
        '" . $_POST["lastname"] . "',
        '" . $_POST["Prenom"] . "', 
        '" . $_POST["contact"] . "',
        '" . $_POST["Mot_de_pass"] . "')");
    header('location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vente</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/publier.css">
    <link rel="stylesheet" href="css/animation.css">
    <link rel="stylesheet" href="style.css">
    <style>

    </style>
</head>

<body>

    <div class="titre">
        <header>
            <h1>Nouvel utilisateur</h1>
            <a href="index.php">Acceuil</a>
        </header>
        <form action="user.php" method="POST">
            <p>
                <label for="lastname"> Nom </label>
                <input type="text" id="lastname" name="lastname" required>

            </p>
            <p>
                <label for="Prenom"> Prenom </label>
                <input type="text" id="Prenom" name="Prenom" required>

            </p>
            <p>
                <label for="contact"> contact </label>
                <input type="text" id="contact" name="contact" required>

            </p>
            <p>
                <label for="password"> Mot de pass</label>
                <input type="password" id="Mot_de_pass" name="Mot_de_pass" required>

            </p>
            <p>
                <button>envoyer</button>
            </p>
        </form>
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
    <script src="script/script.js"></script>
</body>

</html>