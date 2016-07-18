<?php
class LostPass extends Languages {

    private $id_user;
    private $val_key;
    private $err_msg;

    private $_modelUser;
    private $_modelUserLostPass;
    private $_mail;

    function __construct() {
        parent::__construct();
        if(!empty($_SESSION['id']))
            exit(header('Location: '.MVC_ROOT.'/Error/Error/404'));
    }

    function DefaultAction() {
        if(!empty($_SESSION['changePassId']) && !empty($_SESSION['changePassKey']))
            include_once(DIR_VIEW."vLostPassForm.php");
        else
            include_once(DIR_VIEW."vLostPass.php");
    }

    function resetPassAction() {
        // Called by lostPass.js
        if(!empty($_SESSION['changePassId']) && !empty($_SESSION['changePassKey'])) {
            if(is_numeric($_SESSION['changePassId']) && strlen($_SESSION['changePassKey']) == 128) {
                if((!empty($_POST['pwd']) && !empty($_POST['pwd_confirm'])) || (!empty($_POST['pp']) && !empty($_POST['pp_confirm']))) {
                    
                    $this->_modelUserLostPass = new mUserLostPass();
                    $this->_modelUserLostPass->setIdUser($_SESSION['changePassId']);

                    if($this->_modelUserLostPass->getKey()) {

                        if($this->_modelUserLostPass->getKey() == $_SESSION['changePassKey'] && $this->_modelUserLostPass->getExpire() >= time()) {
                            if($_POST['pwd_length'] || $_POST['pp_length']) {
                                $this->_modelUser = new mUsers();
                                $this->_modelUser->setId($_SESSION['changePassId']);

                                if(!empty($_POST['pwd']) && !empty($_POST['pwd_confirm']) && $_POST['pwd_length']) {
                                    // change password

                                    if($_POST['pwd'] == $_POST['pwd_confirm']) {
                                        $this->_modelUser->setPassword($_POST['pwd']);
                                        
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
                                    else {
                                        echo $this->txt->Register->badPassConfirm;
                                    }
                                }
                                if(!empty($_POST['pp']) && !empty($_POST['pp_confirm']) && $_POST['pp_length']) {
                                    // change passphrase

                                    if($_POST['pp'] == $_POST['pp_confirm']) {
                                        $this->_modelUser->setPassphrase(urldecode($_POST['pp']));
                                        //$this->_modelUser->setPassphrase($_POST['pp']);
                                        
                                        if($this->_modelUser->updatePassphrase()) {
                                            unset($_SESSION['changePassId']);
                                            unset($_SESSION['changePassKey']);
                                            unset($_SESSION['sendMail']);
                                            $this->_modelUserLostPass->Delete();
                                            echo 'ok@'.$this->txt->LostPass->updateOk;
                                        }
                                        else
                                            echo $this->txt->LostPass->updateErr;
                                    }
                                    else {
                                        echo $this->txt->Register->badPassphraseConfirm;
                                    }
                                }
                            }
                            else {
                                echo $this->txt->Register->passLength;
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

        $this->_modelUserLostPass = new mUserLostPass();
        $this->_modelUserLostPass->setIdUser($this->id_user);

        if(!($this->_modelUserLostPass->getKey())) // Unable to find key
            exit(header('Location: '.MVC_ROOT.'/Error/Error/404'));

        if($this->_modelUserLostPass->getKey() != $this->val_key || $this->_modelUserLostPass->getExpire() < time()) {
            // Different key, send a new mail ?
            $this->err_msg = $this->txt->LostPass->errmessage;
            include_once(DIR_VIEW."vLostPass.php");
        }
        else {
            // Same keys, redirect and show form to change password or passphrase
            $_SESSION['changePassId'] = $id_user;
            $_SESSION['changePassKey'] = $key;
            header('Location: '.MVC_ROOT.'/LostPass');
        }
    }

    function sendMailAction() {
        // Send AGAIN lost pass mail with validation key
        sleep(1);

        if(!isset($_POST['user']))
            include_once(DIR_VIEW."vLostPass.php");
        else {
            $user = $_POST['user'];

            // One mail per minute
            $w = 0;
            $new = 0;

            if(!empty($_SESSION['sendMail'])) {
                if($_SESSION['sendMail']+60 > time()) {
                    $w = 1;
                    $this->err_msg = $this->txt->Validate->wait;
                    include_once(DIR_VIEW."vLostPass.php");
                }
            }

            if($w == 0) {
                // Allowed to send a new mail
                $this->_modelUser = new mUsers();

                if(strpos($user, '@'))
                    $this->_modelUser->setEmail($user);
                else
                    $this->_modelUser->setLogin($user);
                
                if(!($user_mail = $this->_modelUser->getEmail()))
                    exit(header('Location: '.MVC_ROOT.'/Error/Error/404'));
                
                if(!($id_user = $this->_modelUser->getId()))
                    exit(header('Location: '.MVC_ROOT.'/Error/Error/404'));
                
                $this->_modelUserLostPass = new mUserLostPass();
                $this->_modelUserLostPass->setIdUser($id_user);
                if(!($this->_modelUserLostPass->getKey()))
                    $new = 1;

                $key = hash('sha512', uniqid(rand(), true));

                $this->_modelUserLostPass->setKey($key);
                $this->_modelUserLostPass->setExpire(time()+3600);

                if($new == 0)
                    $this->_modelUserLostPass->Update();
                else
                    $this->_modelUserLostPass->Insert();

                $this->_mail = new Mail();
                $this->_mail->setTo($user_mail);
                $this->_mail->setSubject($this->txt->LostPass->subject);
                $this->_mail->setMessage(str_replace("[id_user]", $id_user, str_replace("[key]", $key, $this->txt->LostPass->message)));
                $this->_mail->send();
                $_SESSION['sendMail'] = time();
                
                $this->err_msg = $this->txt->Global->mail_sent;
                include_once(DIR_VIEW."vLostPass.php");
            }
        }
    }
};
?>