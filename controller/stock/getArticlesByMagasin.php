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
require_once "../../model/stocker.php";
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
      // $idmag = $_GET['idmag'] ;
    
    // Lire les données JSON
$donnees = json_decode(file_get_contents("php://input"), true);

if ( !isset($donnees['idmag'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "id magasin manquant."]);
    exit;
}
    // Créer une instance de la classe Database avec les paramètres de connexion fournis
    $database = new database($host, $dbname, $username, $password);
    $db = $database->getConnexion();
    // Créer une instance de la classe commande
    $com = new stocker($db);
    $com->idmag = $donnees['idmag'];
    // Récupération de la liste des commande avec leurs détails
    $listedoc = $com->getArticlesByMagasin($donnees['idmag']);
    $articles = [];
    while ($row = $listedoc->fetch(PDO::FETCH_ASSOC)) {
        $articles[] = $row;
    }

    echo json_encode($articles);
    exit;
}