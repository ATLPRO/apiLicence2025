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

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
// Vérification basique
if (!$data || !isset($data['numcom'])) {
    http_response_code(400);
    echo json_encode(["error" => "Données de commande manquantes."]);
    exit;
}

class PDF extends FPDF
{
    function Header()
    {
        $this->Image('../assets/logo.jpg', 10, 6, 25);
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 7, utf8_decode("REGAL D'AFRIK SCOOPS"), 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, utf8_decode("SOCIÉTÉ COOPÉRATIVE DE TRANSFORMATION DES PRODUITS AGRICOLES"), 0, 1, 'C');
        $this->Cell(0, 6, utf8_decode("N°19/123/CMR/OU/46/237/COS/011001003"), 0, 1, 'C');
        $this->Cell(0, 6, utf8_decode("Téléphone : 692 80 70 56"), 0, 1, 'C');
        $this->Ln(2);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 13);
        $this->Cell(0, 10, utf8_decode("BON D'ACHAT"), 0, 1, 'C');
        $this->Ln(3);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Page ') . $this->PageNo(), 0, 0, 'C');
    }
}

// Instanciation
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 11);

// Informations générales
$pdf->Cell(90, 8, utf8_decode('Numéro : ') . $data['numcom'], 0, 0);
$pdf->Cell(90, 8, utf8_decode('Date : ') . $data['datecom'], 0, 1);
$pdf->Cell(90, 8, utf8_decode('Référence : ') . $data['refcom'], 0, 0);
$pdf->Cell(90, 8, utf8_decode('Fournisseur : ') . $data['nomfour'], 0, 1);
$pdf->Cell(90, 8, utf8_decode('Magasin : ') . $data['nomMag'], 0, 1);
$pdf->Ln(5);

// Entête tableau
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(60, 10, utf8_decode('Désignation'), 1);
$pdf->Cell(30, 10, utf8_decode('Grammage'), 1);
$pdf->Cell(25, 10, utf8_decode('Qté'), 1);
$pdf->Cell(30, 10, 'PU (FCFA)', 1);
$pdf->Cell(35, 10, 'Montant', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
$total = 0;

if (!empty($data['lignes']) && is_array($data['lignes'])) {
    foreach ($data['lignes'] as $ligne) {
        $designation = utf8_decode($ligne['designation']);
        $grammage = utf8_decode($ligne['unite']);
        $qte = $ligne['qteC'];
        $pu = $ligne['puC'];
        $montant = $qte * $pu;
        $total += $montant;

        $pdf->Cell(60, 8, $designation, 1);
        $pdf->Cell(30, 8, $grammage, 1);
        $pdf->Cell(25, 8, $qte, 1);
        $pdf->Cell(30, 8, number_format($pu, 0, ',', ' '), 1);
        $pdf->Cell(35, 8, number_format($montant, 0, ',', ' '), 1);
        $pdf->Ln();
    }

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(145, 10, utf8_decode('TOTAL GÉNÉRAL'), 1);
    $pdf->Cell(35, 10, number_format($total, 0, ',', ' ') . ' FCFA', 1);
} else {
    $pdf->Cell(0, 10, utf8_decode('Aucune ligne de commande trouvée.'), 0, 1);
}

// Générer le PDF
$pdf->Output();
