<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Headers CORS (ajuste l'origine selon ton frontend)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

require_once "../../config/database.php";

if (!isset($_GET['idprod'])) {
    http_response_code(400);
    echo json_encode(["message" => "ID de production manquant."]);
    exit;
}

$idprod = $_GET['idprod'];

// Connexion à la base
$host = $_GET['host'] ;
$dbname = $_GET['dbname'] ;
$username = $_GET['username'] ;
$password = $_GET['password'] ;

try {
     $database = new database($host, $dbname, $username, $password);
    $db = $database->getConnexion();

    // 1. Récupérer les infos générales de la production
    $queryProd = "
        SELECT 
            p.numprod,
            p.refprod,
            p.dateprod,
            p.coutTprod,
            pr.nomPers as nomPersonnel,
            a.desArt,
            dp.qteP as qteProduite,
            m.nomMag as nomMagasin
        FROM production p
        JOIN participer pa ON pa.idprod = p.idprod
        JOIN personnel pr ON pr.idpers = pa.idpers
        JOIN detailproduction dp ON dp.idprod = p.idprod
        JOIN article a ON a.idArt = dp.idArt
        JOIN magasin m ON m.idmag = dp.idMagDest
        WHERE p.idprod = :idprod
        LIMIT 1
    ";
    $stmtProd = $db->prepare($queryProd);
    $stmtProd->execute([':idprod' => $idprod]);
    $production = $stmtProd->fetch(PDO::FETCH_ASSOC);

    if (!$production) {
        http_response_code(404);
        echo json_encode(["message" => "Production non trouvée."]);
        exit;
    }

    // 2. Récupérer les matières premières utilisées
    $queryMatieres = "
       SELECT 
    a.refArt,
    a.desArt,
    u.intituleU AS unite,
    l.qteL,
    l.puL
FROM ligneproduction l
JOIN article a ON a.idArt = l.idArt
JOIN avoir av ON av.idArt = a.idArt
JOIN uniteart u ON u.idU = av.idU
WHERE l.idprod = :idprod

    ";
    $stmtMat = $db->prepare($queryMatieres);
    $stmtMat->execute([':idprod' => $idprod]);
    $matieres = $stmtMat->fetchAll(PDO::FETCH_ASSOC);

    // Réponse JSON
    echo json_encode([
        "production" => $production,
        "matieres" => $matieres
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Erreur serveur : " . $e->getMessage()]);
}
