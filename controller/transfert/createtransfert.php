<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Headers CORS (ajuste l'origine selon ton frontend)
//header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS,GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once "../../config/database.php";
require_once "../../model/transfert.php";

$host = $_GET['host'];
$dbname = $_GET['dbname'];
$username = $_GET['username'];
$password = $_GET['password'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = $db = (new database($host, $dbname, $username, $password))->getConnexion();
    $data = json_decode(file_get_contents("php://input"), true);

    $transf = new transfert($db);
    $transf->numT = $data["numT"];
     $transf->refT = $data["refT"];
    $transf->dateT = $data["dateT"];
    $transf->idMagSrc = $data["idMagSrc"];
    $transf->idMagDest = $data["idMagDest"];
    $transf->idpers = $data["idpers"];
    $transf->lignes = $data["lignes"];

    $result = $transf->creerTransfert();

    if ($result === true) {
        echo json_encode(["success" => true, "message" => "Transfert enregistré avec succès"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => $result["error"]]);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "Méthode non autorisée"]);
}
