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


require_once "../../config/database.php";
require_once "../../model/nommenclature.php";

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
    $nommer = new nomenclature($db);
    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->idArt) || !isset($data->composants)) {
    http_response_code(400);
    echo json_encode(['message' => "Données incomplètes"]);
    exit;
}
 $nommer->idArt = htmlspecialchars($data->idArt);

try {
     //  Supprimer les anciennes lignes
        $nommer->supprimerParRefArt();
    //  Ajouter les nouvelles lignes
    foreach ($data->composants as $comp) {
        $nommer->qteN = htmlspecialchars($comp->qteN);
        $nommer->puN = htmlspecialchars($comp->puN);
        $nommer->idArt = htmlspecialchars($data->idArt );
        $nommer->idArtFils  = htmlspecialchars($comp->idArtFils );

        if (!$nommer->createnommer()) {
            throw new Exception("Erreur lors de l'enregistrement d'un composant.");
        }
    }

    $db->commit();
    http_response_code(201);
    echo json_encode(['message' => "Nomenclature enregistrée avec succès"]);

} catch (Exception $e) {
    $db->rollBack();
    http_response_code(503);
    echo json_encode(['message' => "Échec de l'enregistrement : " . $e->getMessage()]);
}
}
