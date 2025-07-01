<?php
class avoir{
    private $db;
    private $table="avoir";
    
    // Propriétés de unite
    public $idArt;
    public $idU;
    public $qteA;
    public $puA;
    public function __construct($db){
        
            $this->db=$db;
        
    }
     
  

    // Enregistrer une ligne de magasin
    public function createavoir()
    {
        $query = "INSERT INTO $this->table (qteA,puA,idArt,idU) 
        VALUES (:qteA,:puA,:idArt,:idU)";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(":qteA", $this->qteA);
        $stmt->bindParam(":puA", $this->puA);
        $stmt->bindParam(":idArt", $this->idArt);
        $stmt->bindParam(":idU", $this->idU);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    //modification
   public function updateAvoir()
{
    // Vérifie si une ligne existe déjà pour cet article et cette unité
    $queryCheck = "SELECT COUNT(*) FROM $this->table WHERE idArt = :idArt AND idU = :idU";
    $stmtCheck = $this->db->prepare($queryCheck);
    $stmtCheck->bindParam(":idArt", $this->idArt, PDO::PARAM_INT);
    $stmtCheck->bindParam(":idU", $this->idU, PDO::PARAM_INT);
    $stmtCheck->execute();
    $exists = $stmtCheck->fetchColumn();

    if ($exists > 0) {
        // Si la ligne existe, faire un UPDATE
        $query = "UPDATE $this->table 
                  SET qteA = :qteA, puA = :puA 
                  WHERE idArt = :idArt AND idU = :idU";
    } else {
        // Sinon, faire un INSERT
        $query = "INSERT INTO $this->table (qteA, puA, idArt, idU) 
                  VALUES (:qteA, :puA, :idArt, :idU)";
    }

    $stmt = $this->db->prepare($query);
    $stmt->bindParam(":qteA", $this->qteA);
    $stmt->bindParam(":puA", $this->puA);
    $stmt->bindParam(":idArt", $this->idArt, PDO::PARAM_INT);
    $stmt->bindParam(":idU", $this->idU, PDO::PARAM_INT);

    return $stmt->execute();
}


    //pour la commande des matieres premieres
    public function readunite(){
        $query="SELECT a.idArt,a.desArt,u.idU,u.intituleU,v.puA,v.qteA 
        from $this->table  v , article a,uniteart u WHERE a.idArt=v.idArt 
        and v.idU=u.idU and (a.typeArt='matiere premiere' or a.typeArt='divers') and a.supprimer=0";
        $req=$this->db->prepare($query);
        //$req->bindParam(":idArt", $idArt);
    //  $req->bindParam(":idu", $idu);
        $req->execute();
        if($req->rowCount()>0){
         return $req->fetchAll(PDO::FETCH_ASSOC);   
        }else {
             //on retourne la requete
            return []; 
        }
    }
  
  //pour la recharde dans production des produits fini nommer
    public function readAllProduitFini(){
        $query="SELECT a.idArt, a.refArt, a.desArt
                FROM article a
                INNER JOIN nomenclature n ON a.idArt = n.idArt
                WHERE a.typeArt = 'produit fini' and a.supprimer=0
                GROUP BY a.idArt";
        $req=$this->db->prepare($query);
        $req->execute();
        if($req->rowCount()>0){
         return $req->fetchAll(PDO::FETCH_ASSOC);   
        }else {
             //on retourne la requete
            return []; 
        }
    }
}
  