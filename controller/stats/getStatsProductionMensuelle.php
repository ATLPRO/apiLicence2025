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

    // Requête pour statistiques par mois
    $sql = "
        SELECT 
            MONTH(dateprod) AS mois,
            COUNT(*) AS nb_productions,
            SUM(coutTprod) AS total_cout
        FROM production
        WHERE YEAR(dateprod) = YEAR(CURDATE())
        GROUP BY mois
        ORDER BY mois
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute();
    $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Formatage des résultats
    $result = [];
    for ($m = 1; $m <= 12; $m++) {
        $data = array_filter($stats, function($row) use ($m) {
    return $row['mois'] == $m;
});
        if ($data) {
            $entry = array_values($data)[0];
            $result[] = [
                "mois" => $m,
                "nb_productions" => (int) $entry['nb_productions'],
                "total_cout" => (float) $entry['total_cout']
            ];
        } else {
            $result[] = [
                "mois" => $m,
                "nb_productions" => 0,
                "total_cout" => 0
            ];
        }
    }

    echo json_encode(["success" => true, "data" => $result]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
