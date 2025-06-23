<?php
class uniteart{
    private $db;
    private $table="uniteart";
    
    // Propriétés de unite
    public $idU;
    public $intituleU;
    public $QteU;
    public $PuU;
    public function __construct($db){
        
            $this->db=$db;
        
    }
        //Lecture des unites
     public function readAll(){
        $sql = "SELECT * FROM  $this->table WHERE supprimer = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Enregistrer une ligne de unite
    public function createuniteart()
    {
        $query = "INSERT INTO $this->table (intituleU, QteU,PuU) 
        VALUES (:intituleU, :QteU,:PuU)";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(":intituleU", $this->intituleU);
        $stmt->bindParam(":QteU", $this->QteU);
        $stmt->bindParam(":PuU", $this->PuU);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    public function getidu(){
        $query="SELECT idu from $this->table  order by idu desc  LIMIT 0,1";
        $req=$this->db->prepare($query);
        $req->execute();
        if($req->rowCount()>0){
         return $req->fetchAll(PDO::FETCH_ASSOC);   
        }else {
             //on retourne la requete
            return []; 
        }
    }
    public function updateunite()
    {
        $query = "update $this->table set intitule=:intitule,qte=:qte,PU=:PU where idu=:idu";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":idu", $this->idu);
        $stmt->bindParam(":intitule", $this->intitule);
        $stmt->bindParam(":qte", $this->qte);
        $stmt->bindParam(":PU", $this->PU);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
     //suppression cote client unite
   public function supprimerVirtuellement($idU) {
    $sql = "UPDATE $this->table SET supprimer = 1 WHERE idU = :idU";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([':idU' => $idU]);
}
  
}
