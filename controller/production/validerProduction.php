<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Headers CORS (ajuste l'origine selon ton frontend)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

require_once "../../config/database.php";
require_once "../../model/production.php";

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data || empty($data['numprod']) || empty($data['date']) || empty($data['cout']) || empty($data['matieres'])) {
        http_response_code(400);
        echo json_encode(["message" => "Données incomplètes."]);
        exit;
    }

    // Connexion
    $host = $_GET['host'];
    $dbname = $_GET['dbname'];
    $username = $_GET['username'];
    $password = $_GET['password'];

    $dbConnect = new database($host, $dbname, $username, $password);
    $db = $dbConnect->getConnexion();
    $prod = new production($db);

    try {
        $db->beginTransaction();

        // Étape 1 : Enregistrement production
        $idprod = $prod->creerProduction($data);

      // 2. Enregistrement du produit fini dans detailproduction
        $pf = $data['produitFini'];
            $prod->ajouterDetailProduction(
                $idprod, $pf['idArt'], 
                $pf['qte'],
                $data['idMagSource'], 
            $data['idMagDest']
            );
        foreach ($data['matieres'] as $m) {
    $prod->ajouterMatierePremiere($idprod, $m['idArt'], $m['qteL'], $m['puL']);
}


        // Étape 3 : Participation du personnel
        $prod->ajouterParticipation($idprod, $data['idpers'], $data['date']);

        $db->commit();
        echo json_encode(["success" => true, "message" => "Production enregistrée avec succès."]);
    } catch (Exception $e) {
        $db->rollBack();
        http_response_code(500);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}
