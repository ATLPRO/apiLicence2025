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

    // Créer une instance de la classe article
    $article = new article($db);
    $data = json_decode(file_get_contents("php://input"));
  //modification des magasins 
    
  $db->beginTransaction();
 
      // Remplir les propriétés de l'article
     $article->refArt=htmlspecialchars($data->refArt);
     $article->desArt=htmlspecialchars($data->desArt);
     $article->QteUArt=htmlspecialchars($data->QteUArt);
     $article->pA=htmlspecialchars($data->pA);
     $article->PV=htmlspecialchars($data->PV);
     $article->grammage=htmlspecialchars($data->grammage);
     $article->typeArt=htmlspecialchars($data->typeArt);
     $article->stockMin=htmlspecialchars($data->stockMin);
     $article->idFam=htmlspecialchars($data->idFam);
     
       
  
      $result=$article->updatearticle();
      if ($result) {
        // Toutes les opérations ont réussi, on valide la transaction
        $db->commit();
        http_response_code(201); 
        echo json_encode([
        'success' => true,
        'message' => "Article modifié avec succès",
        'refArt' => $article->refArt // facultatif
    ]);
    } else {
        // Une opération a échoué, on annule la transaction
        $db->rollBack();
        http_response_code(503);
        echo json_encode(['message' => "Échec de la modification  de l'article"]);
    }
} 
 else {
    http_response_code(405);
    echo json_encode(['message' => "La méthode n'est pas autorisée"]);
}