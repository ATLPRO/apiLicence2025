<?php
ini_set('display_errors', 1);
ob_clean(); 
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Headers CORS (ajuste l'origine selon ton frontend)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

//require('fpdf.php');
require('../etats/fpdf.php');
$data = json_decode(file_get_contents("php://input"), true);

class PDF extends FPDF
{
    function Header()
    {
        // Logo
        $this->Image('../assets/logo.jpg', 10, 6, 30); // adapter le chemin
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'BON DE PRODUCTION', 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Page ') . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();

$pdf->SetFont('Arial', '', 12);

// Infos générales
$pdf->Cell(0, 10, 'Numero: ' . $data['numprod'], 0, 1);
$pdf->Cell(0, 10, 'Date: ' . $data['date'], 0, 1);
$pdf->Cell(0, 10, utf8_decode('Référence: ') . $data['refprod'], 0, 1);
$pdf->Cell(0, 10, utf8_decode('Quantité à produire: ') . $data['produitFini']['qte'], 0, 1);
$pdf->Ln(5);

// Tableau
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(60, 10, 'Designation', 1);
$pdf->Cell(30, 10, 'Qté', 1);
$pdf->Cell(30, 10, 'PU', 1);
$pdf->Cell(40, 10, 'Total', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);
if (isset($data['matieres']) && is_array($data['matieres'])) {
foreach ($data['matieres'] as $m) {
    $pdf->Cell(60, 10, utf8_decode($m['designation']), 1);
    $pdf->Cell(30, 10, $m['qteL'], 1);
    $pdf->Cell(30, 10, $m['puL'], 1);
    $pdf->Cell(40, 10, $m['qteL'] * $m['puL'], 1);
    $pdf->Ln();
}


$pdf->Cell(120, 10, 'Total', 1);
$pdf->Cell(40, 10, $data['cout'], 1);

$pdf->Output();
}else {
    $pdf->Cell(0, 10, 'Aucune matière première trouvée.', 0, 1);
}