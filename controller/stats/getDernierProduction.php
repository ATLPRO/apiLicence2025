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
  p.idprod,
  p.numprod,
  p.refprod,
  p.dateprod,
  pers.nompers AS personnel,
  art.desArt AS article_fini,
  dp.qteP AS quantite_produite,
  p.coutTprod
FROM 
  production p
JOIN 
  participer part ON p.idprod = part.idprod
JOIN 
  personnel pers ON part.idpers = pers.idpers
JOIN 
  detailproduction dp ON p.idprod = dp.idprod
JOIN 
  article art ON dp.idArt = art.idArt
ORDER BY 
  p.dateprod DESC
LIMIT 3

  ";

  $stmt = $db->prepare($sql);
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode(['success' => true, 'data' => $result]);

} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
