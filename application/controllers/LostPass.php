<?php
namespace application\controllers;
use \library\MVC as l;
use \application\models as m;

class LostPass extends l\Languages {

    private $id_user;
    private $val_key;
    private $err_msg;

    private $_modelUser;
    private $_modelUserLostPass;
    private $_mail;

    private $ppCounter = 0;

    function __construct() {
        parent::__construct(array(
            'mustBeLogged' => false,
            'mustBeValidated' => false
        ));
    }

    function DefaultAction() {
        if(!empty($_SESSION['changePassId']) && !empty($_SESSION['changePassKey'])) {
            $this->_modelUser = new m\Users();
            $this->_modelUser->id = $_SESSION['changePassId'];
            require_once(DIR_VIEW."LostPassForm.php");
        }
        else
            require_once(DIR_VIEW."LostPass.php");
    }

    function ResetPassAction() {
        // Called by lostPass.js
        if(!empty($_SESSION['changePassId']) && !empty($_SESSION['changePassKey'])) {
            if(is_numeric($_SESSION['changePassId']) && strlen($_SESSION['changePassKey']) == 128) {
                if(!empty($_POST['pwd']) || !empty($_POST['pp'])) {

                    $this->_modelUserLostPass = new m\UserLostPass();
                    $this->_modelUserLostPass->id_user = $_SESSION['changePassId'];

                    if($this->_modelUserLostPass->getKey()) {

                        if($this->_modelUserLostPass->getKey() == $_SESSION['changePassKey'] && $this->_modelUserLostPass->getExpire() >= time()) {
                            $this->_modelUser = new m\Users();
                            $this->_modelUser->id = $_SESSION['changePassId'];

                            if(!empty($_POST['pwd'])) {
                                // change password

                                $this->_modelUser->password = password_hash(urldecode($_POST['pwd']), PASSWORD_BCRYPT);

                                if($this->_modelUser->updatePassword()) {
                                    unset($_SESSION['changePassId']);
                                    unset($_SESSION['changePassKey']);
                                    unset($_SESSION['sendMail']);
                                    $this->_modelUserLostPass->Delete();
                                    echo 'ok@'.$this->txt->LostPass->updateOk;
                                }
                                else
                                    echo $this->txt->LostPass->updateErr;
                            }
                            /*if(!empty($_POST['pp'])) {
                                // change passphrase

                                $this->_modelUser->passphrase = urldecode($_POST['pp']);
                                //$this->_modelUser->passphrase = password_hash(urldecode($_POST['pp']), PASSWORD_BCRYPT);

                                if($this->_modelUser->updatePassphrase()) {
                                    if($this->ppCounter >= 2) {
                                        // To do :
                                        // Delete all user's data
                                    }
                                    $this->_modelUser->incrementPpCounter();
                                    unset($_SESSION['changePassId']);
                                    unset($_SESSION['changePassKey']);
                                    unset($_SESSION['sendMail']);
                                    $this->_modelUserLostPass->Delete();
                                    echo 'ok@'.$this->txt->LostPass->updateOk;
                                }
                                else
                                    echo $this->txt->LostPass->updateErr;
                            }*/
                        }
                        else {
                            unset($_SESSION['changePassId']);
                            unset($_SESSION['changePassKey']);
                            unset($_SESSION['sendMail']);
                            echo $this->txt->LostPass->errmessage;
                        }
                    }
                    else {
                        unset($_SESSION['changePassId']);
                        unset($_SESSION['changePassKey']);
                        unset($_SESSION['sendMail']);
                        echo $this->txt->LostPass->errmessage;
                    }
                }
                else {
                    echo $this->txt->Register->form;
                }
            }
            else {
                echo $this->txt->Error->{'default'};
            }
        }
        else {
            echo $this->txt->Error->{'default'};
        }
    }

    function KeyAction($id_user, $key) {
        if(!is_numeric($id_user) || strlen($key) < 128)
            exit(header('Location: '.MVC_ROOT.'/Error/Error/404'));
        $this->id_user = $id_user;
        $this->val_key = $key;

        $this->_modelUserLostPass = new m\UserLostPass();
        $this->_modelUserLostPass->id_user = $this->id_user;

        if(!($this->_modelUserLostPass->getKey())) // Unable to find key
            exit(header('Location: '.MVC_ROOT.'/Error/Error/404'));

        if($this->_modelUserLostPass->getKey() != $this->val_key || $this->_modelUserLostPass->getExpire() < time()) {
            // Different key, send a new mail ?
            $this->err_msg = $this->txt->LostPass->errmessage;
            require_once(DIR_VIEW."LostPass.php");
        }
        else {
            // Same keys, redirect and show form to change password or passphrase
            $_SESSION['changePassId'] = $id_user;
            $_SESSION['changePassKey'] = $key;
            header('Location: '.MVC_ROOT.'/LostPass');
        }
    }

    function SendMailAction() {
        // Send AGAIN lost pass mail with validation key
        sleep(1);

        if(!isset($_POST['user']))
            require_once(DIR_VIEW."LostPass.php");
        else {
            $user = $_POST['user'];

            // One mail per minute
            $w = 0;
            $new = 0;

            if(!empty($_SESSION['sendMail'])) {
                if($_SESSION['sendMail']+60 > time()) {
                    $w = 1;
                    $this->err_msg = $this->txt->Validate->wait;
                    require_once(DIR_VIEW."LostPass.php");
                }
            }

            if($w == 0) {
                // Allowed to send a new mail
                $this->_modelUser = new m\Users();

                if(strpos($user, '@'))
                    $this->_modelUser->email = $user;
                else
                    $this->_modelUser->login = $user;

                if(!($user_mail = $this->_modelUser->getEmail()))
                    exit(header('Location: '.MVC_ROOT.'/Error/Error/404'));

                if(!($id_user = $this->_modelUser->getId()))
                    exit(header('Location: '.MVC_ROOT.'/Error/Error/404'));

                $this->_modelUserLostPass = new m\UserLostPass();
                $this->_modelUserLostPass->id_user = $id_user;
                if(!($this->_modelUserLostPass->getKey()))
                    $new = 1;

                $key = hash('sha512', uniqid(rand(), true));

                $this->_modelUserLostPass->val_key = $key;
                $this->_modelUserLostPass->expire = time()+3600;

                if($new == 0)
                    $this->_modelUserLostPass->Update();
                else
                    $this->_modelUserLostPass->Insert();

                $this->_mail = new l\Mail();
                $this->_mail->_to = $user_mail;
                $this->_mail->_subject = $this->txt->LostPass->subject;
                $this->_mail->_message = str_replace(
                    array("[id_user]", "[key]", "[url_app]"),
                    array($id_user, $key, URL_APP),
                    $this->txt->LostPass->message
                );
                $this->_mail->send();
                $_SESSION['sendMail'] = time();

                $this->err_msg = $this->txt->Global->mail_sent;
                require_once(DIR_VIEW."LostPass.php");
            }
        }
    }
};
?>
