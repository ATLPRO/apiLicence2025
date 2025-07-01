<?php
//header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Origin: http://localhost:5173"); // ou * temporairement
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");


require_once "../../config/database.php";
require_once "../../model/users.php";

// Connexion
if (
    isset($_GET['host']) &&
    isset($_GET['dbname']) &&
    isset($_GET['username']) &&
    isset($_GET['password']) &&
    isset($_GET['email']) &&
    isset($_GET['passwords'])
) {
    $host = $_GET['host'];
    $dbname = $_GET['dbname'];
    $username = $_GET['username'];
    $password = $_GET['password'];
    $email = $_GET['email'];
    $passwords = $_GET['passwords'];

    $database = new database($host, $dbname, $username, $password);
    $db = $database->getConnexion();

    if (!$db) {
        echo json_encode(["error" => "Connexion à la base de données échouée."]);
        exit;
    }

    $user = new users($db);
    $result = $user->readalluser($email, $passwords);

    if ($result) {
        echo json_encode($result);
    } else {
        echo json_encode([]); // Aucun utilisateur trouvé
    }
} else {
    echo json_encode(["error" => "Paramètres manquants"]);
}
