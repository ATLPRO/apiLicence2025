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
require_once "../../model/famille.php";

// Gestion de la prévol (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

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
    $famille = new familleart($db);
    $familleData = json_decode(file_get_contents("php://input"));

     // Remplir les propriétés de la famille
    $famille->abreviationFam = htmlspecialchars($familleData->abreviationFam);
    $famille->intituleFam = htmlspecialchars($familleData->intituleFam);
   $result=$famille->createfamille();
   
    if ($result) {
        // Toutes les opérations ont réussi, on valide la transaction
        $db->commit();
        http_response_code(201);
        echo json_encode(['message' => "Famille enregistrée avec succès"]);
    } else {
        // Une opération a échoué, on annule la transaction
        $db->rollBack();
        http_response_code(503);
        echo json_encode(['message' => "Échec de l'enregistrement de la famille"]);
    }
} 
 else {
    http_response_code(405);
    echo json_encode(['message' => "La méthode n'est pas autorisée"]);
}