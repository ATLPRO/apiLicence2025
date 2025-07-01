<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

require_once "../../config/database.php";
require_once "../../model/commande.php";
require_once "../../model/concerner.php";

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
    $cmd = new commande($db);
    $cmd->numcom = htmlspecialchars($data->numcom);
    $cmd->refcom = htmlspecialchars($data->refcom);
    $cmd->datecom = htmlspecialchars($data->datecom);
    $cmd->montantTcom = htmlspecialchars($data->montantTcom);
    $cmd->idpers = htmlspecialchars($data->idpers);

    $isTransfert = isset($data->idmag_dest) && !empty($data->idmag_dest);

    if ($isTransfert) {
        $cmd->idfour = null;
        $cmd->idmag = htmlspecialchars($data->idmag); // magasin source
        $cmd->idmag_dest = htmlspecialchars($data->idmag_dest); // magasin destination
    } else {
        $cmd->idfour = htmlspecialchars($data->idfour);
        $cmd->idmag = htmlspecialchars($data->idmag); // magasin de réception fournisseur
        $cmd->idmag_dest = null;
    }

    if (!$cmd->createcom()) {
        throw new Exception("Échec création commande");
    }
    $idcom = $db->lastInsertId();

    foreach ($data->lignes as $ligneData) {
        $cnr = new concerner($db);
        $cnr->idcom = $idcom;
        $cnr->idArt = htmlspecialchars($ligneData->idArt);
        $cnr->qteC = htmlspecialchars($ligneData->qteC);
        $cnr->puC = htmlspecialchars($ligneData->puC);
        $cnr->idU = htmlspecialchars($ligneData->idU);

        if ($isTransfert) {
            // Ligne pour le magasin de départ : retrait de stock
            $cnr->idmag = $cmd->idmag; 
            if (!$cnr->createconcerner()) throw new Exception("Échec inserer ligne transfert depart");

            // Ligne miroir pour le magasin d'arrivée : ajout de stock
            $cnr2 = new concerner($db);
            $cnr2->idcom = $idcom;
            $cnr2->idArt = $cnr->idArt;
            $cnr2->qteC = $cnr->qteC;
            $cnr2->puC = $cnr->puC;
            $cnr2->idU = $cnr->idU;
            $cnr2->idmag = $cmd->idmag_dest;
            if (!$cnr2->createconcerner()) throw new Exception("Échec inserer ligne transfert arrivee");

        } else {
            // Cas commande classique
            $cnr->idmag = $cmd->idmag;
            if (!$cnr->createconcerner()) {
                throw new Exception("Échec insertion concerner");
            }
        }
    }

    $db->commit();
    http_response_code(201);
    echo json_encode(["message" => $isTransfert ? "Transfert validé." : "Commande fournisseur validée."]);

} catch (Exception $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(["message" => $e->getMessage()]);
}
