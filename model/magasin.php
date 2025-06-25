<?php
class magasin{
    private $db;
    private $table="magasin";

    public $idMag;
    public $nomMag;
    public $telMag;
   // public $typeMag;
    public $codeMag;
    public $adresseMag;

    public function __construct($db){       
            $this->db=$db;    
        }
    // creation d'un nouveau magasin
public function createmagasin($codeMag, $nomMag, $adresseMag, $telMag){
    $sql = "INSERT INTO " . $this->table . " (codeMag, nomMag, adresseMag, telMag) VALUES (:codeMag, :nomMag, :adresseMag, :telMag)";
  
    $stmt = $this->db->prepare($sql);
  
  
    try {
      return $stmt->execute([
          ':codeMag' => $codeMag,
          ':nomMag' => $nomMag,
          ':adresseMag' => $adresseMag,
          ':telMag' => $telMag,
          //':typeMag' => $typeMag,
      ]);
  } catch (PDOException $e) {
      // Optionnel : log erreur $e->getMessage()
      return false;
  }
  }
    //Recuperer tous les magasins
    public function getAllMagasins() {
        $sql = "SELECT * FROM  . $this->table  WHERE supprimer = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //modification des magasins
    public function updateMagasin($codeMag, $nomMag, $adresseMag, $telMag) {
        $sql = "UPDATE " . $this->table . " SET nomMag = :nomMag, adresseMag = :adresseMag, telMag = :telMag, codeMag = :codeMag WHERE codeMag = :codeMag";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nomMag' => $nomMag,
            ':adresseMag' => $adresseMag,
            ':telMag' => $telMag,
            //':typeMag' => $typeMag,
            ':codeMag' => $codeMag,
        ]);
    }

    //suppression des magasins
    public function deleteMagasin($codeMag) {
        $sql = "DELETE FROM " . $this->table . " WHERE codeMag = :codeMag";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':codeMag' => $codeMag]);
    }
    // pour charger le magasin choisi pour modifier
    public function getOneMagasin($codeMag) {
        $sql = "SELECT * FROM magasin WHERE codeMag = :codeMag";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':codeMag', $codeMag);
    
        if ($stmt->execute()) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }

    //supprimer virtuellement cote client
    public function supprimerVirtuellement($codeMag) {
        $sql = "UPDATE magasin SET supprimer = 1 WHERE codeMag = :codeMag";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':codeMag' => $codeMag]);
    }
    
    // Récupère tous les magasins avec leurs articles en stock
    public function getStocksParMagasin() {
        // Récupérer tous les magasins
        $sql = "SELECT idmag, nomMag, adresseMag FROM magasin where supprimer=0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $magasins = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $resultat = [];

        foreach ($magasins as $mag) {
            // Récupérer les articles de chaque magasin
            $sqlStock = "
               SELECT 
    a.idArt, 
    a.refArt, 
    a.desArt,
    s.qteS,
    u.intituleU
FROM stocker s
JOIN article a ON s.idArt = a.idArt
LEFT JOIN avoir av ON a.idArt = av.idArt
LEFT JOIN uniteart u ON av.idU = u.idU
WHERE s.idmag = :idmag and a.supprimer=0
            ";
            $stmtStock = $this->db->prepare($sqlStock);
            $stmtStock->execute(['idmag' => $mag['idmag']]);
            $articles = $stmtStock->fetchAll(PDO::FETCH_ASSOC);

            $resultat[] = [
                'idmag' => $mag['idmag'],
                'nomMag' => $mag['nomMag'],
                'adresseMag' => $mag['adresseMag'],
                'articles' => $articles
            ];
        }

        return $resultat;
    }

}