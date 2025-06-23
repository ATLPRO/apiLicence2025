<?php
class transfert {
    private $db;
    public $idT, $numT, $dateT, $idMagSrc, $idMagDest, $idPers, $lignes;

    public function __construct($db) {
        $this->db = $db;
    }

    public function creerTransfert() {
        try {
            $this->db->beginTransaction();

            $sql = "INSERT INTO transfert (numT, dateT, idMagSrc, idMagDest, idpers)
                    VALUES (:numT, :dateT, :idMagSrc, :idMagDest, :idpers)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ":numT" => $this->numT,
                ":dateT" => $this->dateT,
                ":idMagSrc" => $this->idMagSrc,
                ":idMagDest" => $this->idMagDest,
                ":idpers" => $this->idpers
            ]);

            $this->idT = $this->db->lastInsertId();

            // Insérer les lignes de transfert
            foreach ($this->lignes as $ligne) {
                $sqlLigne = "INSERT INTO ligneTransfert (idT, idArt, qteT, puT)
                             VALUES (:idT, :idArt, :qteT, :puT)";
                $stmtLigne = $this->db->prepare($sqlLigne);
                $stmtLigne->execute([
                    ":idT" => $this->idT,
                    ":idArt" => $ligne["idArt"],
                    ":qteT" => $ligne["qteT"],
                    ":puT" => $ligne["puT"]
                ]);
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            return ["error" => $e->getMessage()];
        }
    }
}
