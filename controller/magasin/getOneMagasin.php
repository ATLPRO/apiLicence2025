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
require_once "../../model/magasin.php";

$host = $_GET['host'];
$dbname = $_GET['dbname'];
$username = $_GET['username'];
$password = $_GET['password'];
$codeMag = $_GET['codeMag'];

try {
    $database = new database($host, $dbname, $username, $password);
    $db = $database->getConnexion();

    $magasin = new magasin($db);
    $result = $magasin->getOneMagasin($codeMag);

    if ($result) {
        http_response_code(200);
        echo json_encode($result);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Magasin non trouvé."]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Erreur de connexion : " . $e->getMessage()]);
}