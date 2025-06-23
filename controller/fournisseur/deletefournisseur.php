<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Headers CORS (ajuste l'origine selon ton frontend)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS,PUT,DELETE ,GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

require_once "../../config/database.php";
require_once "../../model/fournisseur.php";



if (isset($_GET['host']) && isset($_GET['dbname']) && isset($_GET['username']) && isset($_GET['password'])) {
    $host = $_GET['host'];
    $dbname = $_GET['dbname'];
    $username = $_GET['username'];
    $password = $_GET['password'];
    
    // Créer une instance de la classe Database avec les paramètres de connexion fournis
    $database = new database($host, $dbname, $username, $password);
    $db = $database->getConnexion();

    // Créer une instance de la classe fournisseur
    $fournisseur = new fournisseur($db);
    $data = json_decode(file_get_contents("php://input"));

$result = $fournisseur->deletefournisseur(htmlspecialchars($data->codefour));

echo json_encode([
    "success" => $result,
    "message" => $result ? "Fournisseur supprimé avec succès" : "Échec de la suppression"
]);
}