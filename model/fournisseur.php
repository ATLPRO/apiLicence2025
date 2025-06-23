<?php
class fournisseur{
    private $db;
    private $table="fournisseur";
    
    // Propriétés du fournisseur
    public $idfour;
    public $codefour;
    public $nomfour;
    public $prenomfour;
    public $tel1four;
    public $tel2four;
    public $adressefour;
    public $soldefour;
    public $cafour;
    public $soldeinitfour;

    public function __construct($db){
        
            $this->db=$db;
        
    }
        //Lecture des fournisseur
     public function readAll(){
        //On n'écris la requete
        $sql="SELECT * FROM $this->table";
       
    //on execute la requette
        $req=$this->db->prepare($sql);
        $req->execute();
        if($req->rowCount()>0){
         return $req->fetchAll(PDO::FETCH_ASSOC);   
        }else {
             //on retourne la requete
            return []; 
        }
    }
    public function readadressef($codef){
        $query="SELECT tel1four,adressefour from $this->table where codefour = :codefour";
        $req=$this->db->prepare($query);
        $req->bindParam(":codefour", $codefour);
        $req->execute();
        if($req->rowCount()>0){
         return $req->fetchAll(PDO::FETCH_ASSOC);   
        }else {
             //on retourne la requete
            return []; 
        }
    }
   

    // Enregistrer une ligne de fournisseur
    public function createfournisseur()
    {
        $query = "insert into $this->table(codefour,nomfour,prenomfour,tel1four,tel2four,adressefour,soldefour,cafour,soldeinitfour) 
        values(:codefour,:nomfour,:prenomfour,:tel1four,:tel2four,:adressefour,:soldefour,:cafour,:soldeinitfour)";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(":codefour",$this->codefour);
        $stmt->bindParam(":nomfour",$this->nomfour);
        $stmt->bindParam(":prenomfour",$this->prenomfour);
        $stmt->bindParam(":tel1four",$this->tel1four);
        $stmt->bindParam(":tel2four",$this->tel2four);
        $stmt->bindParam(":adressefour",$this->adressefour);
        $stmt->bindParam(":soldefour",$this->soldefour);
        $stmt->bindParam(":cafour",$this->cafour);
        $stmt->bindParam(":soldeinitfour",$this->soldeinitfour);
     
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    //suppression cote client four
    public function supprimerVirtuellement($codefour) {
        $sql = "UPDATE $this->table SET supprimer = 1 WHERE codefour = :codefour";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':codefour' => $codefour]);
    }
    // pour charger le magasin choisi pour modifier
    public function getOneFour($codefour) {
        $sql = "SELECT * FROM fournisseur WHERE codefour = :codefour";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':codefour', $codefour);
    
        if ($stmt->execute()) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }
    //valider la modification
    public function updatefournisseur()
    {
        $query = "update $this->table set nomfour=:nomfour,prenomfour=:prenomfour,tel1four=:tel1four,tel2four=:tel2four,adressefour=:adressefour,soldefour=:soldefour,cafour=:cafour,soldeinitfour=:soldeinitfour
         where codefour=:codefour";

        $stmt = $this->db->prepare($query);

       $stmt->bindParam(":codefour",$this->codefour);
        $stmt->bindParam(":nomfour",$this->nomfour);
        $stmt->bindParam(":prenomfour",$this->prenomfour);
        $stmt->bindParam(":tel1four",$this->tel1four);
        $stmt->bindParam(":tel2four",$this->tel2four);
        $stmt->bindParam(":adressefour",$this->adressefour);
        $stmt->bindParam(":soldefour",$this->soldefour);
        $stmt->bindParam(":cafour",$this->cafour);
        $stmt->bindParam(":soldeinitfour",$this->soldeinitfour);
     
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}