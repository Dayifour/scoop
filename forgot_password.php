<?php
require __DIR__ . '/vendor/autoload.php'; // Assurez-vous que ce chemin est correct
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "scoopbd";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'check_email') {
        $email = $_POST['email'];
        $reset_code = $_POST['reset_code'];
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $sql_insert = "INSERT INTO password_resets (email, reset_code, created_at) VALUES ('$email', '$reset_code', NOW())";

            if ($conn->query($sql_insert) === TRUE) {

                $_SESSION['email'] = $email;
                echo json_encode(['status' => 'success', 'message' => 'envoie reussis']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'enregistrement du code.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Cet email n\'existe pas.']);
        }
    } elseif ($action === 'verify_code') {
        $reset_code = $_POST['reset_code'];
        $email = $_SESSION['email'];
        $sql = "SELECT * FROM password_resets WHERE email = '$email' AND reset_code = '$reset_code' AND created_at > NOW() - INTERVAL 15 MINUTE";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Code incorrect ou expiré.']);
        }
    } elseif ($action === 'reset_password') {
        $new_password = password_hash($_POST["new_password"], PASSWORD_DEFAULT);
        $email = $_SESSION['email'];
        $sql = "UPDATE users SET password = '$new_password' WHERE email = '$email'";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(['status' => 'success', 'message' => 'Mot de passe réinitialisé avec succès.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la réinitialisation du mot de passe.']);
        }
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de Passe Oublié</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            padding: 20px;
        }

        .container {
            max-width: 500px;
            margin: auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .step {
            display: none;
        }

        .active {
            display: block;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Mot de Passe Oublié</h2>

        <!-- Étape 1: Demander l'email -->
        <div id="step1" class="step active">
            <label for="email">Entrez votre email</label>
            <input type="email" id="email" placeholder="Votre email" required>
            <button onclick="sendEmail()">Suivant</button>
        </div>

        <!-- Étape 2: Vérification de l'email -->
        <div id="step2" class="step">
            <h3>Vérification de votre email...</h3>
            <input type="text" id="verificationCode" placeholder="Entrez le code reçu" required>
            <button onclick="verifyCode()">Vérifier</button>
        </div>

        <!-- Étape 3: Choisir un nouveau mot de passe -->
        <div id="step3" class="step">
            <label for="newPassword">Nouveau mot de passe</label>
            <input type="password" id="newPassword" placeholder="Nouveau mot de passe" required>
            <button onclick="resetPassword()">Réinitialiser le mot de passe</button>
        </div>
    </div>

    <script>
        let currentStep = 1;

        function showStep(step) {
            document.querySelector(`#step${currentStep}`).classList.remove('active');
            currentStep = step;
            document.querySelector(`#step${currentStep}`).classList.add('active');
        }

        function sendEmail() {
            const email = document.getElementById('email')?.value;
            if (!email) {
                alert("Veuillez entrer un e-mail valide !");
                return;
            }

            const reset_code = Math.floor(100000 + Math.random() * 900000);
            console.log("Code de réinitialisation :", reset_code);

            // Assurez-vous que l'élément existe
            const toElement = document.getElementById("email");
            if (!toElement) {
                alert("L'élément MyEmail est introuvable !");
                return;
            }
            const to = email;

            const subject = "Nouveau Message";
            const message = `Nom : Dou\nAdresse : ${email}\n\nCode : ${reset_code}`;
            console.log("Message envoyé :" + message);

            fetch('https://codingmailer.onrender.com/send-email', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        to: to,
                        subject: subject,
                        message: message
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Erreur lors de l\'envoi de l\'e-mail.');
                        });
                    }
                    alert('E-mail envoyé avec succès.');
                })
                .catch(error => {
                    console.error("Erreur lors de la requête :", error);

                    // Vérifie si errorContainer existe avant d'essayer de l'utiliser
                    const errorContainer = document.getElementById('errorContainer');
                    if (errorContainer) {
                        errorContainer.textContent = "Erreur : " + error.message;
                    } else {
                        alert("Erreur : " + error.message);
                    }
                });
            fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        action: 'check_email',
                        email,
                        reset_code
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showStep(2);
                    } else {
                        alert(data.message);
                    }
                });
        }

        function verifyCode() {
            const reset_code = document.getElementById('verificationCode').value;

            fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        action: 'verify_code',
                        reset_code
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showStep(3);
                    } else {
                        alert(data.message);
                    }
                });
        }

        function Passrest() {
            console.log(document.getElementById("MyEmail"));
            var to = document.getElementById("MyEmail");
            var subject = "Nouveau Message";
            var message = "Nom : " + nom + "\nAdresse : " + adresse + "\n\n" + document.getElementById('msg').value;
            fetch('https://codingmailer.onrender.com/send-email', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        to: to,
                        subject: subject,
                        message: message
                    })
                })
                .then(function(response) {
                    if (response.ok) {
                        alert('E-mail envoyé avec succès.');
                        // Réinitialiser les champs du formulaire
                        document.getElementById('nom').value = '';
                        document.getElementById('adresse').value = '';
                        document.getElementById('message').value = '';
                        document.getElementById('errorContainer').textContent = ''; // Effacer le message d'erreur précédent
                    } else {
                        response.json().then(function(data) {
                            var errorMessage = data && data.message ? data.message : 'Erreur lors de l\'envoi de l\'e-mail.';
                            document.getElementById('errorContainer').textContent = 'Erreur : ' + errorMessage;
                        });
                    }
                })
                .catch(function(error) {
                    console.log('Erreur lors de la requête :', error);
                    document.getElementById('errorContainer').textContent = 'Erreur lors de la requête : ' + error;
                });
        }

        function resetPassword() {
            const new_password = document.getElementById('newPassword').value;

            fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        action: 'reset_password',
                        new_password
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                    } else {
                        alert(data.message);
                    }
                });
        }
    </script>

</body>

</html>