<?php
class familleart{
    private $db;
    private $table="familleart";
    
    // Propriétés de famille
    public $idFam;
    public $abreviationFam;
    public $intituleFam;

    public function __construct($db){
        
            $this->db=$db;
        
    }
        //Lecture des familles
     public function readAll(){
        $sql = "SELECT * FROM " . $this->table ;
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
   // Enregistrer une ligne de famille
    public function createfamille()
    {
        $query = "INSERT INTO $this->table (abreviationFam, intituleFam) VALUES (:abreviationFam, :intituleFam)";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(":abreviationFam", $this->abreviationFam);
        $stmt->bindParam(":intituleFam", $this->intituleFam);
       
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    public function updatefamille()
    {
        $query = "update $this->table set abreviation=:abreviation,intituleart=:intituleart where abreviation=:abreviation";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(":abreviation", $this->abreviation);
        $stmt->bindParam(":intituleart", $this->intituleart);
        

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    public function deletefamille()
    {
        $query = "delete from $this->table where idfam=:idfam";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(":idfam", $this->idfam);
        

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}