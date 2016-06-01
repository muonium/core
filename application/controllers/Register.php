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

        include_once(DIR_VIEW.'vRegister.php');
    }

    function addUserAction() {

        if(!empty($_POST['mail']) && !empty($_POST['pseudo']) && !empty($_POST['pass']) && !empty($_POST['pass_confirm']) && !empty($_POST['passphrase']) && !empty($_POST['passphrase_confirm'])) 
        {
            if($_POST['passlength'])
            {
                if($_POST['pass'] == $_POST['pass_confirm'])
                {
                    if($_POST['passphrase'] == $_POST['passphrase_confirm']) 
                    {
                        if($_POST['pass'] != $_POST['passphrase'])
                        {
                            if(filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL))
                            {
                                if(preg_match("/^[A-Za-z0-9_.-]{1,19}$/", $_POST['pseudo']))
                                {
                                    $this->_modelUser = new mUsers();

                                    $this->_modelUser->setEmail($_POST['mail']);
                                    $this->_modelUser->setPassphrase($_POST['passphrase']);
                                    $this->_modelUser->setPassword($_POST['pass']);
                                    $this->_modelUser->setLogin($_POST['pseudo']);

                                    if(!($this->_modelUser->EmailExists()))
                                    {
                                        if(!($this->_modelUser->LoginExists()))
                                        {
                                            //if(!mkdir('../nova/'.$this->_modelUser->getidUtilisateur(),0600,true )) {
                                            //echo "<p id='ErreurDossier'> Le dossier de l'utilisateur na pas pu être créer ! </p>";
                                            //}
                                            //var_dump($this->_modelUser);
                                            if($this->_modelUser->Insertion())
                                                echo "ok@".htmlentities($this->txt->Register->ok);
                                            else
                                                echo htmlentities($this->txt->Register->error);
                                        }
                                        else {
                                            // "loginExists" response
                                            echo htmlentities($this->txt->Register->loginExists);
                                        }
                                    }
                                    else {
                                        // "mailExists" response
                                        echo htmlentities($this->txt->Register->mailExists);
                                    }
                                }
                                else {
                                    // "loginFormat" response
                                    echo htmlentities($this->txt->Register->loginFormat);
                                }
                            }
                            else {
                                // "mailFormat" response
                                echo htmlentities($this->txt->Register->mailFormat);
                            }
                        }
                        else {
                            // "passEqualPassphrase" response
                            echo htmlentities($this->txt->Register->passEqualPassphrase);
                        }
                    }
                    else {
                        // "badPassphraseConfirm" response
                        echo htmlentities($this->txt->Register->badPassphraseConfirm);
                    }
                }
                else {
                    // "badPassConfirm" response
                    echo htmlentities($this->txt->Register->badPassConfirm);
                }
            }
            else {
                // "passLength" response
                echo htmlentities($this->txt->Register->passLength);
            }
        }
        else {
            // "form" response
            echo htmlentities($this->txt->Register->form);
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
