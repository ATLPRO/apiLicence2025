<?php
class nomenclature{
    private $db;
    private $table="nomenclature";
    
    // Propriétés de unite
    public $idArtFils ;
  public $idArt;
    public $qteN;
    public $puN;
    public function __construct($db){
        
            $this->db=$db;
        
    }
   // Enregistrer une ligne 
    public function createnommer()
    {
        $query = "INSERT INTO $this->table (qteN,puN,idArt,idArtFils) 
        VALUES (:qteN,:puN,:idArt,:idArtFils)";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(":qteN", $this->qteN);
        $stmt->bindParam(":puN", $this->puN);
        $stmt->bindParam(":idArt", $this->idArt);
        $stmt->bindParam(":idArtFils", $this->idArtFils);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // supprimer l'ancienne nommenclature et accepter la nouvelle
    public function supprimerParRefArt() {
    $query = "DELETE FROM $this->table WHERE idArt = :idArt";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':idArt', $this->idArt);
    return $stmt->execute();
}

    //recherche les elements qui ont une meme referart
    public function readallreffils($refArt)
    {
        // Écrivez la requête SQL pour obtenir la liste des factures avec leurs détails
        $query = "SELECT n.refArtFils,n.qteN,a.desArt,n.puN FROM $this->table n,article a
         WHERE   a.refArt=n.refArtFils and n.refArt=:refArt";
        // Préparez la requête
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(":refArt", $refArt);
        // Exécutez la requête
        $stmt->execute();

        // Vérifiez si des données ont été trouvé
        if ($stmt->rowCount() > 0) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
           // return $stmt->fetchColumn(); 

        } /* else {
            return [];
       } */
    }

    // Récupère les composants d'un article fini donné lors de la production
    public function getComposantsByIdArt($idArt) {
        $sql = "SELECT 
                    f.idArt as idArtFils,
                    f.refArt AS refArtFils,
                    f.desArt,
                    f.typeArt,
                    f.stockable,
                    n.qteN,
                    n.puN,
                    ua.intituleU
                FROM nomenclature n
                INNER JOIN article f ON f.idArt = n.idArtFils
                LEFT JOIN avoir av ON av.idArt = f.idArt
                LEFT JOIN uniteart ua ON ua.idU = av.idU
                WHERE n.idArt = :idArt";
        
       $req=$this->db->prepare($sql);
    $req->bindParam(":idArt", $idArt);
    $req->execute();
    if($req->rowCount()>0){
     return $req->fetchAll(PDO::FETCH_ASSOC);   
    }else {
         //on retourne la requete
        return []; 
    }  
    }
}