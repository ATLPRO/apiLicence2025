<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Réponse à la requête OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    http_response_code(200);
    exit();
}

// Headers CORS et JSON
//header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Origin: http://localhost:5173"); // ou * temporairement
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// Connexion à la base
$host = 'localhost';
$dbname = 'licence2025';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Erreur de connexion à la base de données."]);
    exit;
}

// Charger le contrôleur
require_once __DIR__ . '/userController.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email']) || !isset($data['new_password'])) {
    http_response_code(400);
    echo json_encode(["message" => "Champs requis manquants."]);
    exit;
}

$email = $data['email'];
$newPassword = $data['new_password'];

$userController = new userController($pdo);
$result = $userController->resetPassword($email, $newPassword);

if ($result['success']) {
    http_response_code(200);
} else {
    http_response_code(400);
}
echo json_encode($result);