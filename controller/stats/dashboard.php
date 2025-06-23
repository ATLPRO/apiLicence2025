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

$host = $_GET['host'];
$dbname = $_GET['dbname'];
$username = $_GET['username'];
$password = $_GET['password'];

$db = (new Database($host, $dbname, $username, $password))->getConnexion();

$data = [
  'articles' => $db->query("SELECT COUNT(*) FROM article WHERE supprimer=0")->fetchColumn(),
  'fournisseurs' => $db->query("SELECT COUNT(*) FROM fournisseur WHERE supprimer=0")->fetchColumn(),
  'commandes' => $db->query("SELECT COUNT(*) FROM commande WHERE supprimer=0")->fetchColumn(),
  'magasins' => $db->query("SELECT COUNT(*) FROM magasin WHERE supprimer=0")->fetchColumn(),
];

echo json_encode($data);
