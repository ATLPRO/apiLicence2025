<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Headers CORS (ajuste l'origine selon ton frontend)
//header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS,DELETE,GET,PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

require_once "../../config/database.php";
require_once "../../model/magasin.php";

// Gestion de la prévol (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Connexion à la base (paramètres via GET ou par défaut)
$host = $_GET['host'];
$dbname = $_GET['dbname'];
$username = $_GET['username'];
$password = $_GET['password'];

// Lire les données JSON
$donnees = json_decode(file_get_contents("php://input"), true);

if (!isset($donnees['codeMag'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Code du magasin manquant."]);
    exit;
}

try {
    $database = new database($host, $dbname, $username, $password);
    $db = $database->getConnexion();
    $magasin = new magasin($db);
    $resultat = $magasin->supprimerVirtuellement($donnees['codeMag']);

    if ($resultat) {
        echo json_encode(["success" => true, "message" => "Magasin supprimé  avec succès."]);
    } else {
        echo json_encode(["success" => false, "message" => "Échec de la suppression."]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>