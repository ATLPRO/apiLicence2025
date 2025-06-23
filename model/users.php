<?php
class users{
    private $db;
    private $table="users";
    

    public $id;
    public $nom;
    public $email;
    public $passwords;
    public $role;
    public function __construct($db){       
            $this->db=$db;    
        }
// pour les session utilisateurs
public function readalluser($email, $passwords) {
        $sql = "SELECT u.id,u.nom,u.email,u.passwords,u.role,p.idpers,p.nompers 
        FROM $this->table u,personnel p WHERE email = :email AND passwords = :passwords
         and p.id=u.id" ;
    
        $req = $this->db->prepare($sql);
        $req->bindParam(':email', $email);
        $req->bindParam(':passwords', $passwords);
        $req->execute();
    
        return $req->fetch(PDO::FETCH_ASSOC); // Retourne une ligne ou false
    }
// creation d'un nouveau user
public function createuser($nom, $email, $password, $role){
  $sql = "INSERT INTO " . $this->table . " (nom, email, passwords, role) VALUES (:nom, :email, :passwords, :role)";

  $stmt = $this->db->prepare($sql);

  // Hash du mot de passe pour sécurité
  $password = $this->passwords;

  try {
    return $stmt->execute([
        ':nom' => $nom,
        ':email' => $email,
        ':passwords' => $password,
        ':role' => $role,
    ]);
} catch (PDOException $e) {
    // Optionnel : log erreur $e->getMessage()
    return false;
}
}

// reinitialiser le mot de passe
public function findByEmail($email) {
  $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->execute([$email]);
  return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function updatePassword($email, $password) {
  $stmt = $this->db->prepare("UPDATE users SET passwords = ? WHERE email = ?");
  return $stmt->execute([$password, $email]);
}
  public function readAll(){
        $sql = "SELECT * FROM " . $this->table ;
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}   