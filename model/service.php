<?php
class service{
    private $db;
    private $table="service";
    
    // Propriétés de service
    public $idserv;
    public $intituleServ;

    public function __construct($db){
        
            $this->db=$db;
        
    }
        //Lecture des service
     public function readAll(){
        $sql = "SELECT * FROM " . $this->table ;
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
   // Enregistrer une ligne de service
    public function createservice()
    {
        $query = "INSERT INTO $this->table (intituleServ) VALUES (:intituleServ)";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(":intituleServ", $this->intituleServ);
       
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
   
    public function deleteservice()
    {
        $query = "delete from $this->table where idserv=:idserv";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(":idserv", $this->idserv);
        

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}