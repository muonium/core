<?php

class Register extends Languages {

    private $_modelUser;

	public static function getInstance() {
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}

		return self::$instance;
	}

	function DefaultAction() {

		include_once('./application/views/vRegister.php');
	}

    function addUser() {

        if(!empty($_POST['mail']) && !empty($_POST['pseudo']) && !empty($_POST['pass']) && !empty($_POST['pass_confirm']) &&                                        !empty($_POST['passphrase']) && !empty($_POST['passphrase_confirm']) ) {
            if($_POST['pass'] == $_POST['pass_confirm'] && $_POST['passphrase'] == $_POST['passphrase_confirm']) {
                $this->_modelUser = new mUtilisateur();
                $sqlUser = "INSERT INTO utilisateur (id, idUtilisateur, Email, pseudo, password, passPhrase) VALUES (:id, :idUtilisateur, :Email, :pseudo, :password, :passPhrase)";

                $this->_modelUser->setRequete($sqlUser);
                $this->_modelUser->setEmail($_POST['mail']);
                $this->_modelUser->setPassPhrase($_POST['passphrase']);
                $this->_modelUser->setPassword($_POST['pass']);
                $this->_modelUser->setPseudo($_POST['pseudo']);
                $this->_modelUser->setidUtilisateur($this->_modelUser->GenerateId());
                $_SESSION['Utilisateur']['idUtilisateur'] = $this->_modelUser->getidUtilisateur();
                if(!mkdir('../nova/'.$this->_modelUser->getidUtilisateur(),0600,true )) {
                    echo "<p id='ErreurDossier'> Le dossier de l'utilisateur na pas pu être créer ! </p>";
                }
               //var_dump($this->_modelUser);
                if($this->_modelUser->Insertion())
                    echo "ok";
            }
        }
    }
    function GenerateSession( ) {
        echo  $_SESSION['Utilisateur']['idUtilisateur'];
        //$_SESSION['Utilisateur']['Pseudo'] = $this->_modelUser->getPseudo();
        unset ($_SESSION['Utilisateur']['idUtilisateur']);
        unset ($_SESSION['Utilisateur']);
    }

    function uploadKeys() {

        $ErrorKey = array();
         if(!empty($_POST['privateKey']) ) {
             if(!mkdir('../proton/'. $_SESSION['Utilisateur']['idUtilisateur'],0600,true ))
                 echo "<p id='ErrDossierkey'> Erreur lors de la création du dossier des clés </p>";

            $private  = fopen('../proton/'. $_SESSION['Utilisateur']['idUtilisateur'].'/privateKey.gpg','x');
            if(fwrite($private, $_POST['privateKey']))
                fclose($private);
            else
                $ErrorKey['Private'] = false;

            if(!isset($ErrorKey['Private']) && !isset($ErrorKey['Public']) )
                echo "ok";
        }
    }
}
?>
