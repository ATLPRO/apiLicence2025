<?php
ini_set('display_errors', 1);
ob_clean(); 
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Headers CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

require('../etats/fpdf.php');
$data = json_decode(file_get_contents("php://input"), true);

class PDF extends FPDF
{
    function Header()
    {
        // Logo
        $this->Image('../assets/logo.jpg', 10, 6, 25); // logo à gauche
        // Nom entreprise
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 7, utf8_decode("REGAL D'AFRIK SCOOPS"), 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, utf8_decode("SOCIETE COOPERATIVE SIMPLIFIEE DE TRANSFORMATION DES PRODUITS AGRICOLES DU CAMEROUN"), 0, 1, 'C');
        $this->Cell(0, 6, utf8_decode("N°19/123/CMR/OU/46/237/COS/011001003 "), 0, 1, 'C');
        $this->Cell(0, 6, utf8_decode("Téléphone : 692 80 70 56 "), 0, 1, 'C');
        $this->Ln(2);
        // Ligne de séparation
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(5);
        // Titre
        $this->SetFont('Arial', 'B', 13);
        $this->Cell(0, 10, utf8_decode('BON DE PRODUCTION'), 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Page ') . $this->PageNo(), 0, 0, 'C');
    }
}
/* function convertir_en_lettres($nombre)
{
    $f = new NumberFormatter("fr", NumberFormatter::SPELLOUT);
    $lettres = $f->format($nombre);
    return ucfirst($lettres);
} */

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Infos générales
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(90, 8, 'Numero : ' . $data['numprod'], 0, 0);
$pdf->Cell(90, 8, 'Date : ' . $data['date'], 0, 1);
$pdf->Cell(90, 8, utf8_decode('Référence : ') . $data['refprod'], 0, 0);
$pdf->Cell(90, 8, utf8_decode('Quantité à produire : ') . $data['produitFini']['qte'], 0, 1);
$pdf->Ln(5);

// Tableau des matières premières
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(60, 10, utf8_decode('Désignation'), 1);
$pdf->Cell(40, 10, utf8_decode('Qté'), 1);
$pdf->Cell(30, 10, 'PU', 1);
$pdf->Cell(50, 10, 'Montant', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 11);
if (isset($data['matieres']) && is_array($data['matieres']) && count($data['matieres']) > 0) {
    foreach ($data['matieres'] as $m) {
        $pdf->Cell(60, 10, utf8_decode($m['designation']), 1);
        $pdf->Cell(40, 10, $m['qteL'], 1);
        $pdf->Cell(30, 10, $m['puL'], 1);
        $pdf->Cell(50, 10, $m['qteL'] * $m['puL'], 1);
        $pdf->Ln();
    }

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(130, 10, 'Total', 1);
    $pdf->Cell(50, 10, $data['cout'] . ' FCFA', 1);
    // Total en lettres
/* $pdf->Ln(10);
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 10, utf8_decode("Arrêté le présent bon de production à la somme de : ") . utf8_decode(convertir_en_lettres($data['cout'])) . " francs CFA.");
 */
} else {
    $pdf->Cell(0, 10, 'Aucune matière première trouvée.', 0, 1);
}

$pdf->Output();
