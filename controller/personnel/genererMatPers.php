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
// Connexion DB
require_once "../../config/database.php";

$host = $_GET['host'];
$dbname = $_GET['dbname'];
$username = $_GET['username'];
$password = $_GET['password'];

$database = new database($host, $dbname, $username, $password);
$db = $database->getConnexion();
// genere un numero de format com001
try {
    // Requête pour récupérer le dernier numéro
    $query = "SELECT matriculePers FROM personnel ORDER BY idpers DESC LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $lastNum = $stmt->fetchColumn();

    // Si aucune commande encore enregistrée
    if (!$lastNum) {
        $newNum = "MAT001";
    } else {
        // Extraire la partie numérique et incrémenter
        $num = (int) filter_var($lastNum, FILTER_SANITIZE_NUMBER_INT);
        $num++; // incrémentation
        $newNum = "MAT" . str_pad($num, 3, "0", STR_PAD_LEFT);
    }

    echo json_encode(['matriculePers' => $newNum]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Erreur : ' . $e->getMessage()]);
}
?>
