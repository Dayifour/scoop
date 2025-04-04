<?php
session_start();
if (!isset($_SESSION["id"])) {
    header('location: index.php');
    exit;
}

if (isset($_GET['id'])) {
    $productId = intval($_GET['id']);
    $bd = new PDO('mysql:host=localhost;dbname=scoopbd', 'root');

    // Vérifier si le produit appartient à l'utilisateur connecté
    $query = $bd->prepare("SELECT * FROM produit WHERE id = :id AND id_vendeur = :id_vendeur");
    $query->execute(['id' => $productId, 'id_vendeur' => $_SESSION["id"]]);
    $product = $query->fetch();

    if ($product) {
        // Supprimer l'image associée
        if (!empty($product['image'])) {
            $imagePath = __DIR__ . '/uploads/' . $product['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Supprimer le produit
        $deleteQuery = $bd->prepare("DELETE FROM produit WHERE id = :id");
        $deleteQuery->execute(['id' => $productId]);
        header('location: vente.php?message=Produit supprimé avec succès');
        exit;
    } else {
        header('location: vente.php?error=Produit introuvable ou non autorisé');
        exit;
    }
} else {
    header('location: vente.php?error=ID de produit manquant');
    exit;
}
