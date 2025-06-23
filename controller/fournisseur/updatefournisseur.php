<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Headers CORS (ajuste l'origine selon ton frontend)
//header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS,PUT,");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

require_once "../../config/database.php";
require_once "../../model/fournisseur.php";

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
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

    // Créer une instance de la classe fournisseur
    $fournisseur = new fournisseur($db);
    $data = json_decode(file_get_contents("php://input"));
  //modification des magasins 
    
  $db->beginTransaction();
 
       // Remplir les propriétés du fournisseur
       $fournisseur->codefour=htmlspecialchars($data->codefour);
       $fournisseur->nomfour=htmlspecialchars($data->nomfour);
       $fournisseur->prenomfour=htmlspecialchars($data->prenomfour);
       $fournisseur->tel1four=htmlspecialchars($data->tel1four);
       $fournisseur->tel2four=htmlspecialchars($data->tel2four);
       $fournisseur->adressefour=htmlspecialchars($data->adressefour);
       $fournisseur->soldefour=htmlspecialchars($data->soldefour);
       $fournisseur->cafour=htmlspecialchars($data->cafour);
       $fournisseur->soldeinitfour=htmlspecialchars($data->soldeinitfour);
       
  
      $result=$fournisseur->updatefournisseur();
      if ($result) {
        // Toutes les opérations ont réussi, on valide la transaction
        $db->commit();
        http_response_code(201);
        echo json_encode(['message' => "Fournisseur modifié avec succès"]);
    } else {
        // Une opération a échoué, on annule la transaction
        $db->rollBack();
        http_response_code(503);
        echo json_encode(['message' => "Échec de la modification  du fournisseur"]);
    }
} 
 else {
    http_response_code(405);
    echo json_encode(['message' => "La méthode n'est pas autorisée"]);
}