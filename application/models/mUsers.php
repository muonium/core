<?php
    class mUsers extends Model {
        
        /*
            1   id              int(11)         AUTO_INCREMENT
            2   login           varchar(20)
            3   password        varchar(40)
            4   email           varchar(254)
            5   passphrase      varchar(64)
        */
        
        private $id;
        private $login;
        private $password;
        private $email;
        private $passphrase;
        
        /* ******************** SETTER ******************** */
            
        function setLogin($login) {
            $this->login = $login;
        }
        
        function setEmail($email) {
            $this->email = $email;
        }
            
        function setPassphrase($pp){
            $this->passphrase = $pp;
        }
        
        function setPassword($p) {
            $this->password = $p;
        }
        
        /* ******************** GETTER ******************** */
        function getId() {
            return $this->id;
        }
        
        function getEmail() {
            return $this->email;
        }
        
        function getPassphrase() {
             return $this->passphrase;
        }
        
        function getPassword() {
             return $this->password;
        }
            
        function getLogin() {
             return $this->login;
        }
        
        /*function GenerateId() {
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
        }*/
        
        /*function Insertion() {
            
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
        }*/
        
        /*function Update() {
            
        }*/
        
        /*function Delete($idUser) {
            
        }*/
        
		/*function Connection() {
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
		}*/

        
        /*function getPassPhraseByIdUtilisateur() {
        	$pdo = $this->_InstancePDO->prepare($this->_RequeteSql);
        	
        	$idUtilisateur = $this->getidUtilisateur();
        	$pdo->bindValue("idUtilisateur",$idUtilisateur);
        	
        	$pdo->execute();
        	
        	if($row = $pdo->fetch(PDO::FETCH_ASSOC)) {
        		$pp = $row['passPhrase'];
        	}
        	
        	return $pp;
        }*/
    }
?>