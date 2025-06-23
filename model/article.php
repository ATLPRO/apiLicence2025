<?php
class article{
    private $db;
    private $table="article";
    
   public $idArt;
    public $refArt;
    public $desArt;
    public $QteUArt;
    public $pA;
    public $PV;
    public $grammage;
    public $typeArt;
    public $stockMin;
    public $idFam;
    

    public function __construct($db){       
            $this->db=$db;    
        }
        //Lecture des articles
     public function readAll(){
        $sql="SELECT a.idArt,a.refArt,a.desArt,a.QteUArt,a.pA,a.PV,a.grammage,a.typeArt,a.stockMin,f.intituleFam
         FROM $this->table a left join familleart f on f.idFam=a.idFam WHERE supprimer = 0";
        $req=$this->db->prepare($sql);
        $req->execute();
        if($req->rowCount()>0){
         return $req->fetchAll(PDO::FETCH_ASSOC);   
        }else {
             
            return []; 
        }
    }
    
    public function stockmin($referart){
        $sql="SELECT stockmin
         FROM $this->table WHERE referart=:referart";
        $req=$this->db->prepare($sql);
        $req->bindParam(":referart", $referart);
        $req->execute();
        if($req->rowCount()>0){
         return $req->fetchAll(PDO::FETCH_ASSOC);   
        }else {
             
            return []; 
        }
    }
    public function PA($referart){
        $sql="SELECT PA
         FROM $this->table WHERE referart=:referart";
        $req=$this->db->prepare($sql);
        $req->bindParam(":referart", $referart);
        $req->execute();
        if($req->rowCount()>0){
         return $req->fetchAll(PDO::FETCH_ASSOC);   
        }else {
             
            return []; 
        }
    }
    public function updatestockmin($referart,$stockmin){
        $query="UPDATE $this->table SET stockmin=:stockmin where referart=:referart";
        
        $req=$this->db->prepare($query);
        $req->bindParam(":referart", $referart);
        $req->bindParam(":stockmin", $stockmin);
        $req->execute();
        //on retourne la requete
            return []; 
     }
     //selection des articles matieres premiere pour faire une nommenclature du produit fini
    public function getAllMatPre(){
        $sql="SELECT 
    av.qteA,
    av.puA,
    u.intituleU,
    a.idArt,
    CONCAT(a.refArt, ' ', a.desArt) AS reference 
FROM 
    avoir av
JOIN 
    uniteart u ON av.idU = u.idU
JOIN 
    article a ON av.idArt = a.idArt
WHERE 
    (a.typeArt = 'matiere premiere' OR a.typeArt = 'Divers')
    AND a.supprimer = 0
";
        $req=$this->db->prepare($sql);
        $req->execute();
        if($req->rowCount()>0){
         return $req->fetchAll(PDO::FETCH_ASSOC);   
        }else {
             
            return []; 
        }
    }
   //Enregistrement des articles
    public function createarticle()
    {
        $query = "INSERT INTO $this->table (refArt,desArt,QteUArt,pA,PV,grammage,typeArt,stockMin,idFam) values(:refArt,:desArt,:QteUArt,:pA,:PV,:grammage,:typeArt,:stockMin,:idFam)";
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(":refArt",$this->refArt); 
        $stmt->bindParam(":desArt",$this->desArt);
        $stmt->bindParam(":QteUArt",$this->QteUArt);
        $stmt->bindParam(":grammage",$this->grammage);
        $stmt->bindParam(":pA",$this->pA);
        $stmt->bindParam(":PV",$this->PV);
        $stmt->bindParam(":typeArt",$this->typeArt);
        $stmt->bindParam(":stockMin",$this->stockMin);
        $stmt->bindParam(":idFam",$this->idFam);

    if ($stmt->execute()) {
         $lastId = $this->db->lastInsertId(); // récupère le dernier id auto-incrémenté
    return ["success" => true, "idArt" => $lastId];
           
        } else {
            return false;
        }
    }
   //suppression cote client article
   public function supprimerVirtuellement($refArt) {
    $sql = "UPDATE $this->table SET supprimer = 1 WHERE refArt = :refArt";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([':refArt' => $refArt]);
}
    public function updatearticle()
    {
        $query = "update $this->table 
        set desArt=:desArt,QteUArt=:QteUArt,pA=:pA,PV=:PV,grammage=:grammage,typeArt=:typeArt,stockMin=:stockMin,idFam=:idFam
         where refArt=:refArt";

        $stmt = $this->db->prepare($query);

        
        $stmt->bindParam(":refArt",$this->refArt); 
        $stmt->bindParam(":desArt",$this->desArt);
        $stmt->bindParam(":QteUArt",$this->QteUArt);
        $stmt->bindParam(":grammage",$this->grammage);
        $stmt->bindParam(":pA",$this->pA);
        $stmt->bindParam(":PV",$this->PV);
        $stmt->bindParam(":typeArt",$this->typeArt);
        $stmt->bindParam(":stockMin",$this->stockMin);
        $stmt->bindParam(":idFam",$this->idFam);
     if ($stmt->execute()) {
            return ["success" => true, "refArt" => $this->refArt];
        } else {
            return false;
        }
    }
      //infos sur le detail de la commande selectionnee
  public function  refDesTypeArt($refArt)  {
    $sql=" SELECT * 
         from   article
        WHERE refArt = :refArt";

    $req=$this->db->prepare($sql);
    $req->bindParam(":refArt", $refArt);
    $req->execute();
    if($req->rowCount()>0){
     return $req->fetchAll(PDO::FETCH_ASSOC);   
    }else {
         //on retourne la requete
        return []; 
    }  
}
//recuperer les infos pour le detail de larticle produit fini(l'entete)
public function  detailart($refArt)  {
    $sql=" SELECT a.*,f.intituleFam
         from   article a,familleart f
        WHERE a.idFam=f.idFam and refArt=:refArt";

    $req=$this->db->prepare($sql);
    $req->bindParam(":refArt", $refArt);
    $req->execute();
    if($req->rowCount()>0){
     return $req->fetchAll(PDO::FETCH_ASSOC);   
    }else {
         //on retourne la requete
        return []; 
    }  
}
//recuperer les infos pour le detail de larticle produit fini(l'le corps)
public function  matPrePourProduitfini($idArt)  {
    $sql=" SELECT
    n.idArtFils,
  child.refArt ,
  child.desArt ,
  n.qteN ,
  n.puN ,
  u.intituleU 
FROM nomenclature n
  -- on joint l’article “fils” pour récupérer sa référence et sa désignation
  INNER JOIN article child
    ON child.idArt = n.idArtFils
    -- on joint la table `avoir` pour connaître l’unité de cet article
  LEFT JOIN avoir av
    ON av.idArt = child.idArt
  -- et enfin la table des unités
  LEFT JOIN uniteart u
    ON u.idU = av.idU
WHERE
  n.idArt= :idArt"
;

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
      
    public function gettypefini(){
        $query="SELECT designationart from $this->table where typeart='Produit fini '";
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