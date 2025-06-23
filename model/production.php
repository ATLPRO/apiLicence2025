<?php
class production{
    private $db;
    private $table="production";
    
    // Propriétés de production
    public $idprod;
    public $numprod;
    public $dateprod;
    public $refprod;
    public $coutTprod;

    public function __construct($db){
        
            $this->db=$db;
        
    }
      public function creerProduction($data) {
        $query = "INSERT INTO production(numprod, refprod, dateprod, coutTprod)
                  VALUES(:numprod, :refprod, :dateprod, :coutTprod)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ":numprod" => $data["numprod"],
            ":refprod" => $data["refprod"],
            ":dateprod" => $data["date"],
            ":coutTprod" => $data["cout"]
        ]);
        return $this->db->lastInsertId();
    }

    public function ajouterDetailProduction($idprod, $idArt, $qte,$idMagSource,$idMagDest) {
        $query = "INSERT INTO detailproduction(idArt, idprod, qteP,idMagSource,idMagDest) 
        VALUES(:idArt, :idprod, :qte, :idMagSource, :idMagDest)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ":idArt" => $idArt,
            ":idprod" => $idprod,
            ":qte" => $qte,
            ":idMagSource" => $idMagSource,
        ":idMagDest" => $idMagDest
        ]);
    }

    public function ajouterParticipation($idprod, $idpers, $date) {
        $query = "INSERT INTO participer(idprod, idpers, datePa) VALUES(:idprod, :idpers, :date)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ":idprod" => $idprod,
            ":idpers" => $idpers,
            ":date" => $date
        ]);
    }
    public function ajouterMatierePremiere($idprod, $idArt, $qte,$pu) {
        $query = "INSERT INTO ligneproduction(idArt, idprod, qteL,puL) 
        VALUES(:idArt, :idprod, :qte, :pu)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ":idArt" => $idArt,
            ":idprod" => $idprod,
            ":qte" => $qte,
            ":pu" => $pu
        ]);
    }
     public function readAllpro(){
        //On n'écris la requete
        $sql="SELECT * from production";
       
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
}