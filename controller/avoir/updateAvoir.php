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
require_once "../../model/avoir.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $data = json_decode(file_get_contents("php://input"));
    $host = $_GET['host'];
    $dbname = $_GET['dbname'];
    $username = $_GET['username'];
    $password = $_GET['password'];

    $database = new database($host, $dbname, $username, $password);
    $db = $database->getConnexion();
    $db->beginTransaction();

    $avoir = new avoir($db);
    $avoir->idArt = (int) $data->idArt;
    $avoir->idU = (int) $data->idU;
    $avoir->qteA = $data->qteA;
    $avoir->puA = $data->puA;

    $result = $avoir->updateAvoir();

    if ($result) {
        // Mettre à jour la table stocker (juste idU)
        $queryStocker = "UPDATE stocker SET idU = :idU WHERE idArt = :idArt";
        $stmtStocker = $db->prepare($queryStocker);
        $stmtStocker->bindParam(":idU", $avoir->idU, PDO::PARAM_INT);
        $stmtStocker->bindParam(":idArt", $avoir->idArt, PDO::PARAM_INT);
        $stmtStocker->execute();

        // Valider la transaction
        $db->commit();
        http_response_code(201);
        echo json_encode(['message' => "Avoir et stocker modifiés avec succès"]);
    } else {
        $db->rollBack();
        http_response_code(503);
        echo json_encode(['message' => "Échec de la modification"]);
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => "Méthode non autorisée"]);
}
