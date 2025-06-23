<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS, GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

require_once "../../config/database.php";
require_once "../../model/concerner.php";

if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
    http_response_code(200);
    exit;
}

if (isset($_GET['host'], $_GET['dbname'], $_GET['username'], $_GET['password'])) {
    $host = $_GET['host'];
    $dbname = $_GET['dbname'];
    $username = $_GET['username'];
    $password = $_GET['password'];

      $database = new database($host, $dbname, $username, $password);
    $db = $database->getConnexion();

    $concerner = new concerner($db);

    //$idcom = $_GET['idcom'] ;
$donnees = json_decode(file_get_contents("php://input"), true);
    if (!isset($donnees['idcom'])) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(["error" => "idcom requis"]);
        exit;
    }
$idcom = $donnees['idcom'];
    $stmt = $concerner->getLignesCommande($idcom);

    if ($stmt) {
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($rows);
    } else {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(["error" => "Erreur lors de la récupération des données"]);
    }
}
?>
