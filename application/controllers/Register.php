<?php

class Register extends Languages {

    private $_modelUser;
    private $_modelUserVal;
    private $_Bruteforce;
    private $_mail;

    function __construct() {
        parent::__construct();
        if(!empty($_SESSION['id']))
            exit(header('Location: '.MVC_ROOT.'/Error/Error/404'));
        // Initialize the anti-bruteforce class
        $this->_Bruteforce = new AntiBruteforce();
        $this->_Bruteforce->setFolder(ROOT.DS."tmp");
        $this->_Bruteforce->setSID();
        $this->_Bruteforce->setNbMaxAttemptsPerHour(50);
    }

    function DefaultAction() {
        include_once(DIR_VIEW.'vRegister.php');
    }

    function addUserAction() {

        // Sleep during 3s to avoid a big number of requests (bruteforce)
        sleep(3);
        $this->_Bruteforce->Control();
        if($this->_Bruteforce->getError() == 0)
        {
            if(!empty($_POST['mail']) && !empty($_POST['login']) && !empty($_POST['pass']) && !empty($_POST['pass_confirm']) && !empty($_POST['passphrase']) && !empty($_POST['passphrase_confirm']))
            {
                if($_POST['pass'] == $_POST['pass_confirm'])
                {
                    if($_POST['passphrase'] == $_POST['passphrase_confirm'])
                    {
                        if($_POST['pass'] != $_POST['passphrase'])
                        {
                            if(filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL))
                            {
                                if(preg_match("/^[A-Za-z0-9_.-]{2,19}$/", $_POST['login']))
                                {
                                    $this->_modelUser = new mUsers();

                                    $this->_modelUser->setEmail($_POST['mail']);
                                    $this->_modelUser->setPassphrase(urldecode($_POST['passphrase']));
                                    //$this->_modelUser->setPassphrase($_POST['passphrase']);
                                    $this->_modelUser->setPassword($_POST['pass']);
                                    $this->_modelUser->setLogin($_POST['login']);

                                    if(!($this->_modelUser->EmailExists()))
                                    {
                                        if(!($this->_modelUser->LoginExists()))
                                        {
                                            if($_POST['doubleAuth'] == 'true')
                                                $this->_modelUser->setDoubleAuth(1);

                                            if($this->_modelUser->Insertion())
                                            {
                                                // Send registration mail with validation key
                                                $id_user = $this->_modelUser->getLastInsertedId();
                                                $_SESSION['id'] = $id_user;
                                                $key = hash('sha512', uniqid(rand(), true));

                                                $this->_modelUserVal = new mUserValidation();
                                                $this->_modelUserVal->setIdUser($id_user);
                                                $this->_modelUserVal->setKey($key);
                                                $this->_modelUserVal->Insert();

                                                $this->_mail = new Mail();
                                                $this->_mail->setTo($_POST['mail']);
                                                $this->_mail->setSubject($this->txt->Register->subject);
                                                $this->_mail->setMessage(str_replace("[id_user]", $id_user, str_replace("[key]", $key, $this->txt->Register->message)));
                                                $this->_mail->send();

                                                // Create user folder
                                                mkdir(NOVA.'/'.$id_user, 0600);
                                                $_SESSION['validate'] = 1;

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
