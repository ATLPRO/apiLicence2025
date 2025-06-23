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
require_once "../../model/avoir.php";
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
    //$referart = $_GET['referart'];
   // $idu = $_GET['idu'];

    // Créer une instance de la classe Database avec les paramètres de connexion fournis
    $database = new database($host, $dbname, $username, $password);
    $db = $database->getConnexion();
    // Créer une instance de la classe article
    $avoi = new avoir($db);
    $data = json_decode(file_get_contents("php://input"));

    //$nommer->referart=htmlspecialchars($data->referart);
   
    // Récupération de la liste des articles fils 
    $listeart = $avoi->readunite();
    //echo $listeart;
     if ($listeart !== null) {
        // Facture trouvée avec succès, renvoyer au client C#
        http_response_code(200);
        echo json_encode($listeart);
    }  else {
        // Facture non trouvée
        http_response_code(404);
        echo json_encode(["message" => "intitule non trouvé"]);
    }  
}