<?php
namespace application\controllers;
use \library\MVC as l;
use \application\models as m;

class Register extends l\Languages {
    private $_modelUser;
    private $_modelUserVal;
    private $_modelStorage;
    private $_bruteforce;
    private $_mail;

    function __construct() {
        parent::__construct();
        if(!empty($_SESSION['id']))
            exit(header('Location: '.MVC_ROOT.'/Error/Error/404'));
        // Initialize the anti-bruteforce class
        $this->_bruteforce = new l\AntiBruteforce();
        $this->_bruteforce->setFolder(ROOT.DS."tmp");
        $this->_bruteforce->setSID();
        $this->_bruteforce->setNbMaxAttemptsPerHour(50);
    }

    function DefaultAction() {
        require_once(DIR_VIEW.'Register.php');
    }

    function AddUserAction() {
        // Sleep during 2s to avoid a big number of requests (bruteforce)
        sleep(2);
        $this->_bruteforce->Control();
        if($this->_bruteforce->getError() == 0)
        {
            if(!empty($_POST['mail']) && !empty($_POST['login']) && !empty($_POST['pass']) && !empty($_POST['pass_confirm']))
            {
                if($_POST['pass'] == $_POST['pass_confirm'])
                {
                            if(filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL))
                            {
                                if(preg_match("/^[A-Za-z0-9_.-]{2,19}$/", $_POST['login']))
                                {
                                    $this->_modelUser = new m\Users();
                                    $this->_modelUser->email = $_POST['mail'];
                                    $this->_modelUser->password = password_hash(urldecode($_POST['pass']), PASSWORD_BCRYPT);

                                    $this->_modelUser->login = $_POST['login'];
                                    $this->_modelUser->cek = $_POST['cek'];
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
                                                $this->_modelStorage = new m\Storage();
                                                $this->_modelStorage->id_user = $id_user;
                                                $this->_modelStorage->Insertion();
                                                $this->_modelUserVal = new m\UserValidation();
                                                $this->_modelUserVal->id_user = $id_user;
                                                $this->_modelUserVal->val_key = $key;
                                                $this->_modelUserVal->Insert();
                                                $this->_mail = new l\Mail();
                                                $this->_mail->_to = $_POST['mail'];
                                                $this->_mail->_subject = $this->txt->Register->subject;
                                                $this->_mail->_message = str_replace(
                                                    array("[id_user]", "[key]", "[url_app]"),
                                                    array($id_user, $key, URL_APP),
                                                    $this->txt->Register->message
                                                );
                                                $this->_mail->send();
                                                // Create user folder
                                                mkdir(NOVA.'/'.$id_user, 0770);
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
            echo htmlentities($this->txt->Register->{"bruteforceErr".$this->_bruteforce->getError()});
        }
    }
};
?>
