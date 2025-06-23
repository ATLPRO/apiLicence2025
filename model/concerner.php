<?php
class concerner{
    private $db;
    private $table="concerner";
    
    // Propriétés de concerner
    public $idcom;
    public $idArt;
    public $qteC;
    public $puC;
    public function __construct($db){
        
            $this->db=$db;
        
    }
   

    // Enregistrer une ligne de concerner
    public function createconcerner()
    {
        $query = "INSERT INTO $this->table (qteC,puC,idArt,idcom,idmag)
         VALUES (:qteC,:puC,:idArt,:idcom,:idmag)";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(":qteC", $this->qteC);
        $stmt->bindParam(":puC", $this->puC);
        $stmt->bindParam(":idArt", $this->idArt);
        $stmt->bindParam(":idcom", $this->idcom);
        $stmt->bindParam(":idmag", $this->idmag);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    public function deleteByCommande() {
    $sql = "DELETE FROM concerner WHERE idcom = :idcom";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([':idcom' => $this->idcom]);
}
//pour avoir les lignes de la commande soit le detail de corps 
public function getLignesCommande($idcom) {
  $query = "SELECT a.desArt, cn.qteC, cn.puC,
            u.intituleU FROM commande c 
            JOIN concerner cn ON cn.idcom = c.idcom
             JOIN article a ON cn.idArt = a.idArt
             LEFT JOIN avoir av ON av.idArt = a.idArt
            LEFT JOIN uniteart u ON av.idU = u.idU
             WHERE c.idcom = :idcom";
  $stmt = $this->db->prepare($query);
  $stmt->bindParam(':idcom', $idcom);
  $stmt->execute();
  return $stmt;
}

   }