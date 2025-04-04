<?php
require('./fpdf/fpdf.php'); // Assurez-vous que le chemin est correct.

$bd = new PDO('mysql:host=localhost;dbname=scoopbd', 'root');
if (isset($_GET['id'])) {
    $produit = $bd->query("SELECT * FROM produit WHERE id={$_GET['id']}");
    $prod = $produit->fetch();
    $achats = $bd->query("SELECT * FROM users WHERE id = {$prod['id_vendeur']}");
    $achat = $achats->fetch();
} else {
    die("Produit introuvable.");
}

$pdf = new FPDF();
$pdf->AddPage();

// En-tête
// Ajout de l'image du site
$headerImagePath = './image/Logo5.png'; // Chemin de l'image du site
if (file_exists($headerImagePath)) {
    $pdf->Image($headerImagePath, 10, 10, 30, 30); // Position (x=10, y=10), Taille (30x30)
}

// Ajout du texte publicitaire
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetXY(50, 15); // Position du texte à côté de l'image
$pdf->Cell(0, 10, utf8_decode("Bienvenue sur Scoop - Votre plateforme de confiance"), 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(50, 25);
$pdf->Cell(0, 10, utf8_decode("Achetez et vendez facilement avec nous !"), 0, 1);

$pdf->Ln(20); // Espacement après l'en-tête

// Titre
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode($prod['nom']), 0, 1, 'C');
$pdf->Ln(10);

// Ajout de l'image du produit
if (!empty($prod['photo'])) {
    $imagePath = './uploads/' . $prod['photo'];
    if (file_exists($imagePath)) {
        $pdf->Image($imagePath, 150, 50, 40, 40); // Position (x=150, y=50), Taille (40x40)
    }
}

// Prix
$pdf->SetFont('Arial', '', 14);
$pdf->Cell(0, 10, 'Prix: ' . number_format($prod['prix'], 0, ',', ' ') . ' FCFA', 0, 1);
$pdf->Ln(5);

// Description
if (!empty($prod['Description'])) {
    $pdf->MultiCell(0, 10, utf8_decode("Description:\n" . $prod['Description']));
    $pdf->Ln(5);
}

// Vendeur
$pdf->Cell(0, 10, utf8_decode('Vendeur: ' . $achat['contact']), 0, 1);

// Générer le fichier PDF
$pdf->Output('D', 'Produit_' . $prod['id'] . '.pdf');
