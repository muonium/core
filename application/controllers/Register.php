<?php

class Register extends Languages {

    private $_modelUser;
    private $_modelUserVal;
    private $_Bruteforce;

    function __construct() {
        parent::__construct();
        // Initialize the anti-bruteforce class
        $this->_Bruteforce = new AntiBruteforce();
        $this->_Bruteforce->setFolder(ROOT.DS."tmp");
        $this->_Bruteforce->setIP();
        $this->_Bruteforce->setNbMaxAttemptsPerHour(50);
    }

    function DefaultAction() {
        
        include_once(DIR_VIEW.'vRegister.php');
    }

    function addUserAction() {

        $this->_Bruteforce->Control();
        if($this->_Bruteforce->getError() == 0)
        { 
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
                                                if($this->_modelUser->Insertion())
                                                {
                                                    $this->_modelUserVal = new mUserValidation();
                                                    echo "ok@".htmlentities($this->txt->Register->ok);
                                                }
                                                else {
                                                    // "error" response
                                                    echo htmlentities($this->txt->Register->error);
                                                }
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
        else {
            // Anti-bruteforce returns an error
            echo htmlentities($this->txt->Register->{"bruteforceErr".$this->_Bruteforce->getError()});
        }
    }
};
?>
