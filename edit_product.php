<?php
session_start();
if (!isset($_SESSION["id"])) {
    header('location: index.php');
    exit;
}

$bd = new PDO('mysql:host=localhost;dbname=scoopbd', 'root');

// Vérifier si un ID de produit est passé
if (!isset($_GET['id'])) {
    header('location: vente.php');
    exit;
}

$product_id = $_GET['id'];
$product = $bd->prepare("SELECT * FROM produit WHERE id = :id AND id_vendeur = :id_vendeur");
$product->execute(['id' => $product_id, 'id_vendeur' => $_SESSION["id"]]);
$product = $product->fetch();

if (!$product) {
    header('location: vente.php');
    exit;
}

// Mettre à jour le produit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prix = $_POST['prix'];
    $description = $_POST['description'];

    // Gestion de l'image
    if (!empty($_FILES['photo']['name'])) {
        $oldPhoto = $product['photo'];
        $photoName = time() . '_' . $_FILES['photo']['name'];
        $photoPath = 'uploads/' . $photoName;

        // Supprimer l'ancienne image
        if (file_exists('uploads/' . $oldPhoto)) {
            unlink('uploads/' . $oldPhoto);
        }

        // Déplacer la nouvelle image
        move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath);

        $update = $bd->prepare("UPDATE produit SET nom = :nom, prix = :prix, description = :description, photo = :photo WHERE id = :id AND id_vendeur = :id_vendeur");
        $update->execute([
            'nom' => $nom,
            'prix' => $prix,
            'description' => $description,
            'photo' => $photoName,
            'id' => $product_id,
            'id_vendeur' => $_SESSION["id"]
        ]);
    } else {
        $update = $bd->prepare("UPDATE produit SET nom = :nom, prix = :prix, description = :description WHERE id = :id AND id_vendeur = :id_vendeur");
        $update->execute([
            'nom' => $nom,
            'prix' => $prix,
            'description' => $description,
            'id' => $product_id,
            'id_vendeur' => $_SESSION["id"]
        ]);
    }

    header('location: vente.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un produit - Scoop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Copier les styles de product.php */
        :root {
            --primary: #FF6B00;
            --primary-light: #FF8C42;
            --secondary: #2C3E50;
            --light: #F8F9FA;
            --dark: #212529;
            --white: #FFFFFF;
            --gray: #6C757D;
            --border: #E9ECEF;
            --success: #28A745;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.12);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            --border-radius: 10px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light);
            color: var(--secondary);
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            padding-top: 80px;
        }

        .form-container {
            max-width: 600px;
            margin: 2rem auto;
            background: var(--white);
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
        }

        .form-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            font-size: 1rem;
        }

        .form-group textarea {
            resize: none;
            height: 150px;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            color: var(--white);
            background: var(--primary);
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
        }

        .btn:hover {
            background: var(--primary-light);
        }

        .btn-secondary {
            background: var(--gray);
        }

        .btn-secondary:hover {
            background: var(--dark);
        }
    </style>
</head>

<body>
    <header class="main-header">
        <div class="header-container">
            <a href="index.php">
                <img src="./image/Logo5.png" alt="Scoop" class="logo">
            </a>
            <nav>
                <ul class="nav-links">
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="product.php">Publier</a></li>
                    <li><a href="vente.php" class="active">Activités</a></li>
                    <li><a href="index.php?a">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="form-container">
        <h1 class="form-title">Modifier le produit</h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nom">Nom du produit</label>
                <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($product['nom']) ?>" required>
            </div>
            <div class="form-group">
                <label for="prix">Prix (FCFA)</label>
                <input type="number" id="prix" name="prix" value="<?= htmlspecialchars($product['prix']) ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required><?= htmlspecialchars($product['description']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="photo">Image du produit</label>
                <input type="file" id="photo" name="photo" accept="image/*">
                <p>Image actuelle :</p>
                <img src="uploads/<?= htmlspecialchars($product['photo']) ?>" alt="Produit" style="max-width: 100%; height: auto; margin-top: 10px;">
            </div>
            <button type="submit" class="btn">Enregistrer</button>
            <a href="vente.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>

    <?php include("./components/footer.php") ?>
</body>

</html>