<?php
class stocker{
    private $db;
    private $table="stocker";
    
    // Propriétés de stock
    public $idmag;
    public $qteS;
    public $cmup;
    public $idU;

    public function __construct($db){
        
            $this->db=$db;
        
    }
    //retourner le stock des articles disponible pour la production 
    public function getstockDisponible($idArt,$idmag){
        $query= "SELECT 
    f.refArt AS refArtFils,
    f.desArt,
    n.qteN,
    n.puN,
    ua.intituleU,
    COALESCE(s.qteS, 0) AS qteS 
FROM nomenclature n
INNER JOIN article f ON f.idArt = n.idArtFils
LEFT JOIN avoir av ON av.idArt = f.idArt
LEFT JOIN uniteart ua ON ua.idU = av.idU
LEFT JOIN stocker s ON s.idArt = f.idArt AND s.idMag = :idmag
WHERE n.idArt = :idArt";
//COALESCE() pour remplacer les valeurs nulle par 0
        $req=$this->db->prepare($query);
        $req->bindParam(":idArt", $idArt);
        $req->bindParam(":idmag", $idmag);
        $req->execute();
        if($req->rowCount()>0){
         return $req->fetchAll(PDO::FETCH_ASSOC);   
        }else {
             //on retourne la requete
            return []; 
        }
    }
    /// mettre a jour le stock ou ajouter
    public function updateOrInsert($idArt, $idU, $idmag, $qteAjoutee, $puCommande) {
    try {
        // Vérifier si un stock existe pour cet article, unité, et magasin
        $sql = "SELECT qteS, cmup FROM stocker WHERE idArt = :idArt 
        AND idU = :idU AND idmag = :idmag";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":idArt", $idArt);
        $stmt->bindParam(":idU", $idU);
        $stmt->bindParam(":idmag", $idmag);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Mise à jour du stock existant
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $ancienneQte = floatval($row['qteS']);
            $ancienCMPU  = floatval($row['cmup']);

            $nouvelleQte = $ancienneQte + $qteAjoutee;
            $nouveauCMPU = round(
                (($ancienneQte * $ancienCMPU) + ($qteAjoutee * $puCommande)) / $nouvelleQte,
                2
            );

            $sqlUpdate = "UPDATE stocker 
                          SET qteS = :qteS, cmup = :cmup 
                          WHERE idArt = :idArt AND idU = :idU AND idmag = :idmag";
            $stmtUpdate = $this->db->prepare($sqlUpdate);
            $stmtUpdate->bindParam(":qteS", $nouvelleQte);
            $stmtUpdate->bindParam(":cmup", $nouveauCMPU);
            $stmtUpdate->bindParam(":idArt", $idArt);
            $stmtUpdate->bindParam(":idU", $idU);
            $stmtUpdate->bindParam(":idmag", $idmag);

            return $stmtUpdate->execute();
        } else {
            // Insertion si le stock n'existe pas
            $sqlInsert = "INSERT INTO stocker (idArt, idU, idmag, qteS, cmup) 
                          VALUES (:idArt, :idU, :idmag, :qteS, :cmup)";
            $stmtInsert = $this->db->prepare($sqlInsert);
            $stmtInsert->bindParam(":idArt", $idArt);
            $stmtInsert->bindParam(":idU", $idU);
            $stmtInsert->bindParam(":idmag", $idmag);
            $stmtInsert->bindParam(":qteS", $qteAjoutee);
            $stmtInsert->bindParam(":cmup", $puCommande);

            return $stmtInsert->execute();
        }
    } catch (PDOException $e) {
        error_log("Erreur updateOrInsert : " . $e->getMessage());
        echo json_encode(["message" => "Erreur SQL : " . $e->getMessage()]);
        return false;
    }
}
  // Obtenir les articles par magasin pour le transfert entre les magasins
    public function getArticlesByMagasin($idmag) {
        $query = "SELECT a.idArt, a.desArt, u.intituleU, s.qteS, av.puA, av.idU
                  FROM stocker s
                  JOIN article a ON a.idArt = s.idArt
                  JOIN avoir av ON av.idArt = a.idArt
                  JOIN uniteart u ON u.idU = av.idU
                  WHERE s.idmag = :idmag and a.supprimer=0" ;

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':idmag', $idmag);
        $stmt->execute();

        return $stmt;
    }
}

