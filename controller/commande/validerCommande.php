<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Headers CORS (ajuste l'origine selon ton frontend)
//header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

require_once "../../config/database.php";
// Include tes modèles : commande.php, concerner.php, stocker.php
require_once "../../model/commande.php";
require_once "../../model/concerner.php";
require_once "../../model/stocker.php";


if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
    http_response_code(200);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

$host = $_GET['host'];
$dbname = $_GET['dbname'];
$username = $_GET['username'];
$password = $_GET['password'];

$database = new database($host, $dbname, $username, $password);
$db = $database->getConnexion();
$db->beginTransaction();

try {
    // 1. Créer l'entête de commande
    $cmd = new commande($db);
    $cmd->numcom     = htmlspecialchars($data->numcom);
    $cmd->refcom     = htmlspecialchars($data->refcom);
    $cmd->datecom    = htmlspecialchars($data->datecom);
    $cmd->montantTcom = htmlspecialchars($data->montantTcom);
    $cmd->idpers     = htmlspecialchars($data->idpers);
    $cmd->idfour     = htmlspecialchars($data->idfour);
    //$cmd->idmag      = htmlspecialchars($data->idmag);

    if (!$cmd->createcom()) {
        throw new Exception("Échec création commande");
    }
    $idcom = $db->lastInsertId();

    // 2. Insérer chaque ligne dans concerner et mettre à jour le stock
    foreach ($data->lignes as $ligneData) {
        // – Inserer dans `concerner`
        $cnr = new concerner($db);
        $cnr->idcom = $idcom;
        $cnr->idArt = htmlspecialchars($ligneData->idArt);
        $cnr->qteC  = htmlspecialchars($ligneData->qteC);
        $cnr->puC   = htmlspecialchars($ligneData->puC);
        $cnr->idU   = htmlspecialchars($ligneData->idU);

        if (!$cnr->createconcerner()) {
            throw new Exception("Échec insertion concerner");
        }

        // – Vérifier / mettre à jour le stock
        //   Suppose que $stocker a une méthode updateOrInsert($idU, $idmag, $qteC, $puC)
        $st = new stocker($db);
        if (!$st->updateOrInsert($ligneData->idArt, $ligneData->idU, $data->idmag, $ligneData->qteC, $ligneData->puC)) {
            throw new Exception("Échec mise à jour stock pour l'article " . $ligneData->idArt);
        }

    }

    $db->commit();
    http_response_code(201);
    echo json_encode(["message" => "Commande validée, lignes et stocks mis à jour."]);
} catch (Exception $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(["message" => $e->getMessage()]);
}
