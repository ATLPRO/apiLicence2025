<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Headers CORS
//header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS, GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

require_once "../../config/database.php";

$host = $_GET['host'] ;
$dbname = $_GET['dbname'] ;
$username = $_GET['username'] ;
$password = $_GET['password'] ;

$db = (new database($host, $dbname, $username, $password))->getConnexion();

// Requête 1 : matières premières en stock bas
$sql = "SELECT 
    a.refArt, 
    a.desArt, 
    a.stockMin, 
    s.qteS, 
    u.intituleU, 
    m.nomMag 
FROM stocker s
JOIN article a ON s.idArt = a.idArt
JOIN avoir av ON a.idArt = av.idArt AND av.idU = s.idU
JOIN uniteart u ON av.idU = u.idU
JOIN magasin m ON s.idmag = m.idmag
WHERE 
    a.typeArt = 'matiere premiere' 
    AND a.supprimer = 0
    AND s.qteS < a.stockMin
ORDER BY s.qteS ASC
LIMIT 5";

// Requête 2 : stock total par produit fini
$sql1 = "SELECT 
    a.refArt,
    a.desArt,
    u.intituleU,
    SUM(s.qteS) AS total_qteS
FROM stocker s
JOIN article a ON s.idArt = a.idArt
JOIN avoir av ON av.idArt = a.idArt AND av.idU = s.idU
JOIN uniteart u ON u.idU = av.idU
WHERE 
    a.typeArt = 'produit fini'
    AND a.supprimer = 0
GROUP BY a.idArt, a.refArt, a.desArt, u.intituleU
ORDER BY total_qteS DESC";

$alertes_matiere_premiere = [];
$stocks_produit_fini = [];

try {
    // Exécuter la première requête
    $stmt1 = $db->query($sql);
    while ($row = $stmt1->fetch(PDO::FETCH_ASSOC)) {
        $alertes_matiere_premiere[] = "Stock bas pour l'article « " . $row['desArt'] . " » (Ref: " . $row['refArt'] . ") dans le magasin « " . $row['nomMag'] . " » - Stock actuel : " . $row['qteS'] . " " . $row['intituleU'];
    }

    // Exécuter la deuxième requête
    $stmt2 = $db->query($sql1);
    while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
        $stocks_produit_fini[] = "Stock total de l'article « " . $row['desArt'] . " » (Ref: " . $row['refArt'] . ") : " . $row['total_qteS'] . " " . $row['intituleU'];
    }

    // Préparer la réponse
    $response = [
        'alertes_matiere_premiere' => $alertes_matiere_premiere,
        'stocks_produit_fini' => $stocks_produit_fini
    ];

    header('Content-Type: application/json');
    echo json_encode($response);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => "Erreur SQL : " . $e->getMessage()]);
}
