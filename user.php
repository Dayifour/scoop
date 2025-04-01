<?php
session_start();
$bd = new PDO('mysql:host=localhost;dbname=scoopbd', 'root');
$users = $bd->query('select * from users');

if (isset($_POST['nom'])) {
    foreach ($users as $user) {
        if ($user["Contact"] == $_POST["contact"]) {
            $_SESSION["contact"] = $_POST["contact"];
            header('location: index.php');
            exit;
        }
    }

    // Cripter le mot de passe avant l'insertion
    $hashedPassword = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $bd->exec("INSERT INTO users VALUES ('" . null . "',
        '" . $_POST["nom"] . "',
        '" . $_POST["prenom"] . "', 
        '" . $_POST["contact"] . "',
        '" . "Client" . "',
        '" . $_POST["email"] . "',
        '" . $hashedPassword . "')");
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
    <link rel="stylesheet" href="./css/login.css">
    <style>

    </style>
</head>

<body>

    <div class="titre">
        <header>
            <h1>Nouvel utilisateur</h1>
        </header>
        <div class="login-container">
            <form action="user.php" method="post" class="login-form">
                <h2>Se connecter</h2>
                <div class="form-group">
                    <label for="contact">Prenom</label>
                    <input type="text" id="prenom" name="prenom" placeholder="prenom" required>
                </div>
                <div class="form-group">
                    <label for="contact">Nom</label>
                    <input type="text" id="nom" name="nom" placeholder="nom" required>
                </div>
                <div class="form-group">
                    <label for="contact">Email</label>
                    <input type="text" id="email" name="email" placeholder="email" required>
                </div>
                <div class="form-group">
                    <label for="contact">Contact</label>
                    <input type="text" id="contact" name="contact" placeholder="contact" required>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" placeholder="Mot de passe" required>
                </div>
                <button type="submit">Envoyer</button>
            </form>
        </div>

    </div>

    <script src="script/script.js"></script>
</body>

</html>