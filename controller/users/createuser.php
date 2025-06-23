<?php
// Headers CORS (ajuste l'origine selon ton frontend)
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

// Gestion de la prévol (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Récupération des données JSON envoyées par fetch()
$data = json_decode(file_get_contents("php://input"), true);

if (
    !$data || 
    !isset($data['nom'], $data['email'], $data['passwords'], $data['role']) ||
    empty(trim($data['nom'])) || 
    empty(trim($data['email'])) || 
    empty(trim($data['passwords'])) || 
    empty(trim($data['role']))
) {
    http_response_code(400);
    echo json_encode(["message" => "Tous les champs sont requis."]);
    exit();
}

$nom = trim($data['nom']);
$email = trim($data['email']);
$password = trim($data['passwords']);
$role = trim($data['role']);

require_once "../../config/database.php";
require_once "../../model/users.php";

$database = new database("localhost", "licence2025", "root", "");
$db = $database->getConnexion();

if (!$db) {
    http_response_code(500);
    echo json_encode(["message" => "Erreur connexion base de données."]);
    exit();
}

$user = new users($db);
$created = $user->createuser($nom, $email, $password, $role);

if ($created) {
    echo json_encode(["message" => "Utilisateur créé avec succès"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Erreur lors de la création de l'utilisateur (email peut-être déjà utilisé)."]);
}
