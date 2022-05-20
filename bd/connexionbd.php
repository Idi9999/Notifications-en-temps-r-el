
<?php 

    class connexionBD{
        private $serveur ='localhost';
        private $nom = 'bdagenda';
        private $login = 'root';
        private $pass = '';
        private $bd_port="3307";
        private $connexion;

        function __construct($serveur=null, $nom = null, $login = null, $pass= null, $bd_port=null){
            if($serveur != null){
                $this->serveur = $serveur;
                $this->nom = $nom;
                $this->login = $login;
                $this->pass = $pass;
                $this->bd_port=$bd_port;
            }
            try {
                $this->connexion = new PDO("mysql:host=$this->serveur;port=$this->bd_port;dbname=$this->nom", $this->login, $this->pass);
                $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);       
            }
            catch(PDOException $e){
                echo'Erreur : Impossible de se connecter à cette base de données' ;
                die();
            
            }
           
        }
        public function connexion(){
            return $this->connexion;
        } 
        public function insert($sql, $data=array()){
            $req = $this->connexion->prepare($sql);
            $req->execute($data);
        }
    }
    $BD = new connexionBD;
    $BDD = $BD->connexion();

    
?>