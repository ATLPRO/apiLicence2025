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
require_once "../../model/article.php";
// Prévol (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $host = $_GET['host'];
    $dbname = $_GET['dbname'];
    $username = $_GET['username'];
    $password = $_GET['password'];
   
    
    // Lire les données JSON
$donnees = json_decode(file_get_contents("php://input"), true);

if (!isset($donnees['idArt'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "id article manquant."]);
    exit;
}
    // Créer une instance de la classe Database avec les paramètres de connexion fournis
    $database = new database($host, $dbname, $username, $password);
    $db = $database->getConnexion();
    // Créer une instance de la classe commande
    $com = new article($db);
    $com->idArt = $donnees['idArt'];
    // Récupération de la liste des commande avec leurs détails
    $listedoc = $com->matPrePourProduitfini($donnees['idArt']);
    if (!empty($listedoc)) {
        // Renvoyer la liste des commande au format JSON
        http_response_code(200);
        echo json_encode($listedoc);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucun article trouvé "]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Paramètres de connexion à la base de données manquants"]);
}