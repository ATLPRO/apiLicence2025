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

try {
    // Connexion dynamique via paramètres GET
    $host = $_GET['host'] ;
    $dbname = $_GET['dbname'];
    $username = $_GET['username'];
    $password = $_GET['password'];

    $database = new database($host, $dbname, $username, $password);
    $db = $database->getConnexion();
  $sql = "
    SELECT 
      c.idcom,
      c.refcom,
      c.datecom,
      f.nomfour AS fournisseur,
      c.montantTcom
    FROM 
      commande c
    JOIN 
      fournisseur f ON c.idfour = f.idfour
      where c.supprimer=0
    ORDER BY 
      c.datecom DESC
    LIMIT 3
  ";

  $stmt = $db->prepare($sql);
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode(['success' => true, 'data' => $result]);

} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
