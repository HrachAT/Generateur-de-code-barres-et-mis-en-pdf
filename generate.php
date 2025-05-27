<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;
use FPDF;

function calculCleEAN13($code12): string {
    $sum = 0;
    for ($i = 0; $i < 12; $i++) {
        $sum += (int) $code12[$i] * ($i % 2 === 0 ? 1 : 3);
    }
    return (string) ((10 - ($sum % 10)) % 10);
}

// Récupération des articles
$articles = [];
foreach ($_POST['nom'] as $i => $nom) {
    $code = preg_replace('/[^0-9]/', '', $_POST['code'][$i] ?? '');
    if ($nom && strlen($code) >= 12) {
        $code12 = substr(str_pad($code, 12, '0', STR_PAD_LEFT), 0, 12);
        $code13 = $code12 . calculCleEAN13($code12);
        $articles[] = ['nom' => htmlspecialchars($nom), 'code' => $code13];
    }
}

if (empty($articles)) {
    die("Aucun article valide fourni.");
}

// Configuration PDF
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->SetAutoPageBreak(false);
$pdf->SetFont('Arial', '', 10);
$generator = new BarcodeGeneratorPNG();

// Dimensions des étiquettes
$etiquetteLargeur = 64;     // mm
$etiquetteHauteur = 34;   // mm ← augmenté au max
$margeGauche = 9.5;         // mm
$margeHaut = 13;            // mm
$espacementX = 0.66;         // mm
$espacementY = 0.34;         // mm ← comme demandé
$colonnes = 3;
$lignes = 8;
$parPage = $colonnes * $lignes;

$pdf->AddPage();

foreach ($articles as $index => $article) {
    if ($index > 0 && $index % $parPage == 0) {
        $pdf->AddPage();
    }

    $col = $index % $colonnes;
    $ligne = floor(($index % $parPage) / $colonnes);

    $x = $margeGauche + $col * ($etiquetteLargeur + $espacementX);
    $y = $margeHaut + $ligne * ($etiquetteHauteur + $espacementY);

    // Titre de l’article
    $pdf->SetXY($x + 2, $y + 2);
    $pdf->Cell(60, 5, $article['nom'], 0, 1);

    // Générer le code-barres PNG
    $barcode = $generator->getBarcode($article['code'], $generator::TYPE_EAN_13, 2, 40);
    $tmp = __DIR__ . "/tmp_$index.png";
    file_put_contents($tmp, $barcode);

    // Afficher le code-barres
    $pdf->Image($tmp, $x + 7, $y + 10, 50, 20);
    unlink($tmp);
}

$pdf->Output('I', 'etiquettes.pdf');
