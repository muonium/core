<?php
    class mUtilisateur extends Model {
        private $id;
        private $idUtilisateur;
        private $Email;
        private $pseudo;
        private $password;
        private $passPhrase;
        

        
        /* ******************** SETTER ******************** */
        function setId($id) {
            $this->id = $id;
        }
            
        function setidUtilisateur($id) {
            $this->idUtilisateur = $id;
        }
        
        function setEmail($m) {
            $this->Email = $m;
        }
            
        function setPassPhrase($pp){
            $this->passPhrase = $pp;
        }
        
        function setPassword($p) {
            $this->password = $p;
        }
            
        function setPseudo($p) {
            $this->pseudo = $p;
        }
        
        function setRequete($sqlRequete) {
            $this->_RequeteSql = $sqlRequete;
        }
        /* ******************** GETTER ******************** */
        function getId() {
            return $this->id;
        }

        function getidUtilisateur() {
            return $this->idUtilisateur;
        }
        
        function getEmail() {
            return $this->Email;
        }
        
        function getPassPhrase() {
             return $this->passPhrase;
         }
        
        function getPassword() {
             return $this->password;
         }
            
        function getPseudo() {
             return $this->pseudo;
         }
        
        function getIdByIdUtilisateur() {
        	
        }
        
        function getStockage() {
        	
           $idUtilisateur = $this->getidUtilisateur();
            
           $pdo = $this->_InstancePDO->prepare($this->_RequeteSql);
            $pdo->bindValue(':idUtilisateur',$idUtilisateur);
            $pdo->execute();
            $stockage  = $pdo->fetchAll(PDO::FETCH_CLASS,'mStockage');
            return $stockage;
        }
        
        function GenerateId() {
            $base  = 'AZERTYUIOPQSDFGHJKLMWXCVBNazertyuiopqsdfghjklmwxcvbn0123456789';
            $id = "";
            $sqlUser = "SELECT idUtilisateur FROM utilisateur";
            $_instancePDO = new                       PDO('mysql:host='.confBDD::hostDefaut.';dbname='.confBDD::bddDefaut,confBDD::userDefaut,confBDD::passDefaut);
            $pdo = $_instancePDO->prepare($sqlUser);
            $pdo->execute();
            $user  = $pdo->fetchAll(PDO::FETCH_CLASS,'mUtilisateur');

            foreach($user as $key => $utilisateur)  {
                for($i=0;$i<10;$i++) {
                    $id .= $base[rand(0,61)];
                }
                $userId = $utilisateur->getidUtilisateur();
                if($id != $userId) {
                    break;
                } else {
                    for($i=0;$i<10;$i++) {
                        $id .= $base[rand(0,61)];
                    }
                }
                
            }
            if(empty($id)) {
                for($i=0;$i<10;$i++) {
                    $id .= $base[rand(0,61)];
                }
            }
            return $id;
        }
        
        function Insertion() {
            
            $pdo = $this->_InstancePDO->prepare($this->_RequeteSql);
            $id = $this->getId();
            $idUser =  $this->getidUtilisateur();
            $email = $this->getEmail();
            $username = $this->getPseudo();
            $pass = $this->getPassword();
            $phrase = $this->getPassPhrase();
            
            $pdo->bindValue(":id", $id);
            $pdo->bindValue(":idUtilisateur", $idUser);
            $pdo->bindValue(":Email",$email);
            $pdo->bindValue(":pseudo", $username);
            $pdo->bindValue(":password", $pass);
            $pdo->bindValue(":passPhrase",$phrase);
            
            $retour = $pdo->execute();
                
            return $retour;
        }
        
        function Update() {
            
        }
        
        function Delete($idUser) {
            
        }
        
		function Connection() {
			$pdo = $this->_InstancePDO->prepare($this->_RequeteSql);
			
			$username = $this->getPseudo();
			$pass = $this->getPassword();
			$phrase = $this->getPassPhrase();
			
			$pdo->bindValue(":pseudo", $username);
			$pdo->bindValue(":password", $pass);
			$pdo->bindValue(":passPhrase",$phrase);
			
			$pdo->execute();
			$user = $pdo->fetchAll(PDO::FETCH_CLASS,'mUtilisateur');
			return $user;
		}

        
        function getPassPhraseByIdUtilisateur() {
        	$pdo = $this->_InstancePDO->prepare($this->_RequeteSql);
        	
        	$idUtilisateur = $this->getidUtilisateur();
        	$pdo->bindValue("idUtilisateur",$idUtilisateur);
        	
        	$pdo->execute();
        	
        	if($row = $pdo->fetch(PDO::FETCH_ASSOC)) {
        		$pp = $row['passPhrase'];
        	}
        	
        	return $pp;
        }
    }
?>