<?php
class database{
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $db;

    public function __construct($host,$dbname,$username,$password){
        $this->host=$host;
        $this->dbname=$dbname;
        $this->username=$username;
        $this->password=$password;


    }
    public function getConnexion(){
        $db = null; 
    
        try{
    
            $this->db=new PDO(
                "mysql:host=$this->host;dbname=$this->dbname;charset=utf8",
                 $this->username,
                $this->password,
           [
               PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
               PDO::ATTR_DEFAULT_FETCH_MODE=> PDO::FETCH_ASSOC 
           ]
        );
    
        }catch(\PDOException $e){
            echo "Erreur de connexion : " . $e->getMessage();
        }
        return $this->db;
        }
}
  


    