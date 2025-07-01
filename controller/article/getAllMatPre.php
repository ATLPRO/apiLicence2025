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
require_once "../../model/article.php";

if (isset($_GET['host']) && isset($_GET['dbname']) && isset($_GET['username']) && isset($_GET['password'])) {
    $host = $_GET['host'];
    $dbname = $_GET['dbname'];
    $username = $_GET['username'];
    $password = $_GET['password'];
    // Créer une instance de la classe Database avec les paramètres de connexion fournis
    $database = new database($host, $dbname, $username, $password);
    $db = $database->getConnexion();
    // Créer une instance de la classe article
    $article = new article($db);
    // Récupération de la liste des articles avec leurs détails
    $listeart = $article->getAllMatPre();
    if (!empty($listeart)) {
        // Renvoyer la liste des articles au format JSON
        http_response_code(200);
        echo json_encode($listeart);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucun article trouvé "]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Paramètres de connexion à la base de données manquants"]);
}