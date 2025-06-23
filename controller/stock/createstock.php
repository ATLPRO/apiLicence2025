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
require_once "../../model/stocker.php";

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $host = $_GET['host'];
    $dbname = $_GET['dbname'];
    $username = $_GET['username'];
    $password = $_GET['password'];
   
 // Instancier la base de données avec les paramètres de connexion
 $database = new database($host, $dbname, $username, $password);
 $db = $database->getConnexion();

 // Commencer une transaction
 $db->beginTransaction();
// Créer une instance de la classe famille
    $stock = new stocker($db);
    $data = json_decode(file_get_contents("php://input"));

     // Remplir les propriétés de la famille
    
    $stock->qteS = htmlspecialchars($data->qteS);
    $stock->cmup = htmlspecialchars($data->cmup);
    $stock->idmag = htmlspecialchars($data->idmag);
    $stock->idU = htmlspecialchars($data->idU);
   
    $result=$stock->createstocker();
   
    
    if ($result) {
        // Toutes les opérations ont réussi, on valide la transaction
        $db->commit();
        http_response_code(201);
        echo json_encode(['message' => "Stock enregistré avec succès"]);
    } else {
        // Une opération a échoué, on annule la transaction
        $db->rollBack();
        http_response_code(503);
        echo json_encode(['message' => "Échec de l'enregistrement du stock"]);
    }
} 
 else {
    http_response_code(405);
    echo json_encode(['message' => "La méthode n'est pas autorisée"]);
}