<?php
require __DIR__ . '/vendor/autoload.php';
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "scoopbd";

$conn = new mysqli($servername, $username, $password, $dbname);

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
                echo json_encode(['status' => 'success', 'message' => 'Email envoyé avec succès']);
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
            session_destroy();
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
    <title>Réinitialisation du mot de passe</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            padding: 30px;
            box-sizing: border-box;
        }

        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 24px;
        }

        .step {
            display: none;
        }

        .step.active {
            display: block;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
            transition: border 0.3s;
        }

        input:focus {
            border-color: #4CAF50;
            outline: none;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }

        .error-message {
            color: #f44336;
            margin-top: 10px;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Réinitialiser votre mot de passe</h2>

        <!-- Étape 1: Email -->
        <div id="step1" class="step active">
            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" placeholder="Entrez votre email" required>
            </div>
            <button onclick="sendEmail()">Continuer</button>
            <div id="error1" class="error-message"></div>
        </div>

        <!-- Étape 2: Code de vérification -->
        <div id="step2" class="step">
            <div class="form-group">
                <p>Un code de vérification a été envoyé à votre adresse email.</p>
                <label for="verificationCode">Code de vérification</label>
                <input type="text" id="verificationCode" placeholder="Entrez le code reçu" required>
            </div>
            <button onclick="verifyCode()">Vérifier</button>
            <div id="error2" class="error-message"></div>
        </div>

        <!-- Étape 3: Nouveau mot de passe -->
        <div id="step3" class="step">
            <div class="form-group">
                <label for="newPassword">Nouveau mot de passe</label>
                <input type="password" id="newPassword" placeholder="Entrez votre nouveau mot de passe" required>
            </div>
            <button onclick="resetPassword()">Réinitialiser</button>
            <div id="error3" class="error-message"></div>
        </div>
    </div>

    <script>
        let currentStep = 1;

        function showStep(step) {
            document.querySelectorAll('.step').forEach(el => el.classList.remove('active'));
            currentStep = step;
            document.getElementById(`step${step}`).classList.add('active');
        }

        function showError(step, message) {
            const errorElement = document.getElementById(`error${step}`);
            errorElement.textContent = message;
            setTimeout(() => errorElement.textContent = '', 5000);
        }

        function sendEmail() {
            const email = document.getElementById('email').value.trim();
            if (!email) {
                showError(1, 'Veuillez entrer une adresse email valide');
                return;
            }

            const reset_code = Math.floor(100000 + Math.random() * 900000);
            console.log("Code généré:", reset_code);

            // Envoi du code par email
            fetch('https://codingmailer.onrender.com/send-email', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        to: email,
                        subject: 'Code de réinitialisation',
                        message: `Votre code de réinitialisation est: ${reset_code}\n\nCe code expirera dans 15 minutes.`
                    })
                })
                .then(response => {
                    if (!response.ok) throw new Error('Erreur lors de l\'envoi de l\'email');
                    return response.json();
                })
                .then(() => {
                    // Enregistrement dans la base de données
                    return fetch('', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            action: 'check_email',
                            email,
                            reset_code
                        })
                    });
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showStep(2);
                    } else {
                        showError(1, data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError(1, 'Une erreur est survenue. Veuillez réessayer.');
                });
        }

        function verifyCode() {
            const code = document.getElementById('verificationCode').value.trim();
            if (!code) {
                showError(2, 'Veuillez entrer le code reçu');
                return;
            }

            fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        action: 'verify_code',
                        reset_code: code
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showStep(3);
                    } else {
                        showError(2, data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError(2, 'Une erreur est survenue. Veuillez réessayer.');
                });
        }

        function resetPassword() {
            const newPassword = document.getElementById('newPassword').value.trim();
            if (!newPassword) {
                showError(3, 'Veuillez entrer un nouveau mot de passe');
                return;
            }

            fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        action: 'reset_password',
                        new_password: newPassword
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        window.location.href = 'login.php'; // Rediriger vers la page de connexion
                    } else {
                        showError(3, data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError(3, 'Une erreur est survenue. Veuillez réessayer.');
                });
        }
    </script>
</body>

</html>