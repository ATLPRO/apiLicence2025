<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Headers CORS (ajuste l'origine selon ton frontend)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS,GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

require_once "../../config/database.php";
require_once "../../model/service.php";

// Gestion de la prévol (OPTIONS)
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

    // Créer une instance de la classe unite
    $service = new service($db);
    
    // Récupération de la liste des unites avec leurs détails
    $listeunite = $service->readAll();
    if (!empty($listeunite)) {
        // Renvoyer la liste des unites au format JSON
        http_response_code(200);
        echo json_encode($listeunite);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucun service trouvé "]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Paramètres de connexion à la base de données manquants"]);
}