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
require_once "../../model/personnel.php";

// Prévol (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
if (isset($_GET['host']) && isset($_GET['dbname']) && isset($_GET['username']) && isset($_GET['password'])) {
    $host = $_GET['host'];
    $dbname = $_GET['dbname'];
    $username = $_GET['username'];
    $password = $_GET['password'];

    // Créer une instance de la classe Database avec les paramètres de connexion fournis
    $database = new database($host, $dbname, $username, $password);
    $db = $database->getConnexion();

    $db->beginTransaction();
    // Créer une instance de la classe personel
      $personnel = new personnel($db);
    $data = json_decode(file_get_contents("php://input"));

     // Attribuer les valeurs
        $personnel->matriculePers = htmlspecialchars($data->matriculePers);
        $personnel->nompers = htmlspecialchars($data->nompers);
        $personnel->prenompers = htmlspecialchars($data->prenompers );
        $personnel->datenaispers = $data->datenaispers ;
        $personnel->lieunaispers = htmlspecialchars($data->lieunaispers);
        $personnel->numtelpers = $data->numtelpers ;
        $personnel->numcnipers = htmlspecialchars($data->numcnipers);
        $personnel->datevalidite = $data->datevalidite ;
        $personnel->sexepers = htmlspecialchars($data->sexepers  );
        $personnel->statutpers = htmlspecialchars($data->statutpers);
        $personnel->idfonc = $data->idfonc;
        $personnel->idserv = $data->idserv;

      $result=$personnel->updatepers();
      if ($result) {
        // Toutes les opérations ont réussi, on valide la transaction
        $db->commit();
        http_response_code(201);
        echo json_encode(['message' => "personnel modifié avec succès"]);
    } else {
        // Une opération a échoué, on annule la transaction
        $db->rollBack();
        http_response_code(503);
        echo json_encode(['message' => "Échec de la modification  du personnel"]);
    }
} 
 else {
    http_response_code(405);
    echo json_encode(['message' => "La méthode n'est pas autorisée"]);
}