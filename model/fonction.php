<?php
class fonction{
    private $db;
    private $table="fonction";
    
    // Propriétés de fonction
    public $idfonc;
    public $intituleFonc;

    public function __construct($db){
        
            $this->db=$db;
        
    }
        //Lecture des fonction
     public function readAll(){
        $sql = "SELECT * FROM " . $this->table ;
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
   // Enregistrer une ligne de fonction
    public function createfonction()
    {
        $query = "INSERT INTO $this->table ( intituleFonc) VALUES (:intituleFonc)";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(":intituleFonc", $this->intituleFonc);
       
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
   
    public function deletefonction()
    {
        $query = "delete from $this->table where idfonc=:idfonc";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(":idfonc", $this->idfonc);
        

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}