<?php
class personnel {
    private $db;
    private $table = "personnel";

     public function __construct($db){
         $this->db=$db;
         }

    // Propriétés publiques
    public $matriculePers;
    public $nompers;
    public $prenompers;
    public $datenaispers;
    public $lieunaispers;
    public $numtelpers;
    public $numcnipers;
    public $datevalidite;
    public $sexepers;
    public $statutpers;
    public $idfonc;
    public $idserv;
   // public $id;

    public function createPersonnel() {
        $query = "INSERT INTO $this->table (
            matriculePers, nompers, prenompers, datenaispers, lieunaispers, 
            numtelpers, numcnipers, datevalidite, sexepers, statutpers, 
            idfonc, idserv
        ) VALUES (
            :matriculePers, :nompers, :prenompers, :datenaispers, :lieunaispers, 
            :numtelpers, :numcnipers, :datevalidite, :sexepers, :statutpers, 
            :idfonc, :idserv
        )";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(":matriculePers", $this->matriculePers);
        $stmt->bindParam(":nompers", $this->nompers);
        $stmt->bindParam(":prenompers", $this->prenompers);
        $stmt->bindParam(":datenaispers", $this->datenaispers);
        $stmt->bindParam(":lieunaispers", $this->lieunaispers);
        $stmt->bindParam(":numtelpers", $this->numtelpers);
        $stmt->bindParam(":numcnipers", $this->numcnipers);
        $stmt->bindParam(":datevalidite", $this->datevalidite);
        $stmt->bindParam(":sexepers", $this->sexepers);
        $stmt->bindParam(":statutpers", $this->statutpers);
        $stmt->bindParam(":idfonc", $this->idfonc);
        $stmt->bindParam(":idserv", $this->idserv);
        // $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }
     public function getAll() {
        $sql = "SELECT p.*, f.intituleFonc, s.intituleServ
          FROM personnel p
          LEFT JOIN fonction f ON p.idfonc = f.idfonc
          LEFT JOIN service s ON p.idserv = s.idserv WHERE supprimer=0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    //suppression cote client pers
    public function supprimerVirtuellement($matriculePers) {
        $sql = "UPDATE $this->table SET supprimer = 1 WHERE matriculePers = :matriculePers";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':matriculePers' => $matriculePers]);
    }
    //modifier
    public function updatepers()
    {
        $query = "update $this->table 
        set matriculePers=:matriculePers,nompers=:nompers, prenompers=:prenompers, datenaispers=:datenaispers, lieunaispers=:lieunaispers, 
            numtelpers=:numtelpers, numcnipers=:numcnipers, datevalidite=:datevalidite, sexepers=:sexepers, statutpers=:statutpers, 
            idfonc=:idfonc, idserv=:idserv where matriculePers=:matriculePers";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(":matriculePers", $this->matriculePers);
        $stmt->bindParam(":nompers", $this->nompers);
        $stmt->bindParam(":prenompers", $this->prenompers);
        $stmt->bindParam(":datenaispers", $this->datenaispers);
        $stmt->bindParam(":lieunaispers", $this->lieunaispers);
        $stmt->bindParam(":numtelpers", $this->numtelpers);
        $stmt->bindParam(":numcnipers", $this->numcnipers);
        $stmt->bindParam(":datevalidite", $this->datevalidite);
        $stmt->bindParam(":sexepers", $this->sexepers);
        $stmt->bindParam(":statutpers", $this->statutpers);
        $stmt->bindParam(":idfonc", $this->idfonc);
        $stmt->bindParam(":idserv", $this->idserv);
        

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}

