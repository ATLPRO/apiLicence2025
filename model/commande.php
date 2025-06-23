<?php
class commande{
    private $db;
    private $table="commande";
    
    // Propriétés du document
    public $idcom;
    public $numcom;
    public $refcom;
    public $datecom;
    public $montantTcom;
public $idmag_dest;
public $id;
public $idfour;
    public function __construct($db){
        
            $this->db=$db;
        
    }
        //Lecture des commandes 
     public function readAllcom(){
        //On n'écris la requete
        $sql="SELECT DISTINCT  c.idcom,c.numcom, c.refcom, c.datecom,c.montantTcom,f.nomfour,m.nomMag 
        FROM commande c 
        join fournisseur f on c.idfour=f.idfour 
        join concerner cn on cn.idcom = c.idcom 
        JOIN article a ON cn.idArt = a.idArt 
        LEFT JOIN stocker s on s.idArt=a.idArt 
        LEFT JOIN magasin m ON s.idmag=m.idmag 
        WHERE c.supprimer = 0";
       
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
    //infos sur le detail de la commande selectionnee
  public function  detailcom($numcom)  {
    $sql=" SELECT 
            c.numcom, c.refcom, c.datecom, c.montantTcom,
            f.nomfour,
            a.desArt, cn.qteC, cn.puC,
            u.intituleU
        FROM commande c
        JOIN concerner cn ON cn.idcom = c.idcom
        JOIN article a ON cn.idArt = a.idArt
        LEFT JOIN avoir av ON av.idArt = a.idArt
        LEFT JOIN uniteart u ON av.idU = u.idU

        JOIN fournisseur f ON c.idfour = f.idfour
        WHERE c.numcom = :numcom";
   
    $req=$this->db->prepare($sql);
    $req->bindParam(":numcom", $numcom);
    $req->execute();
    if($req->rowCount()>0){
     return $req->fetchAll(PDO::FETCH_ASSOC);   
    }else {
         //on retourne la requete
        return []; 
    }  
}

    // Enregistrer une ligne de commande
    public function createcom()
    {
        $query = "INSERT INTO $this->table (numcom, refcom, datecom, montantTcom,idpers,idfour) 
        VALUES (:numcom, :refcom, :datecom, :montantTcom,:idpers,:idfour)";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(":numcom", $this->numcom);
        $stmt->bindParam(":refcom", $this->refcom);
        $stmt->bindParam(":datecom", $this->datecom);
        $stmt->bindParam(":montantTcom", $this->montantTcom);
        $stmt->bindParam(":idpers", $this->idpers);
        $stmt->bindParam(":idfour", $this->idfour);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
     //suppression cote client commande
    public function supprimerVirtuellement($numcom) {
        $sql = "UPDATE $this->table SET supprimer = 1 WHERE numcom = :numcom";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':numcom' => $numcom]);
    }
    //modifier une commande
    public function updatecom() {
    $sql = "UPDATE commande SET numcom = :numcom, refcom = :refcom, datecom = :datecom, 
            montantTcom = :montantTcom, idpers = :idpers, idfour = :idfour 
            WHERE idcom = :idcom";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
        ':numcom' => $this->numcom,
        ':refcom' => $this->refcom,
        ':datecom' => $this->datecom,
        ':montantTcom' => $this->montantTcom,
        ':idpers' => $this->idpers,
        ':idfour' => $this->idfour,
        ':idcom' => $this->idcom
    ]);
}

}