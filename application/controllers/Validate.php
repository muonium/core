<?php
namespace application\controllers;
use \library\MVC as l;
use \application\models as m;

class Validate extends l\Languages {

        private $id_user;
        private $val_key;
        private $err_msg;

        private $_modelUser;
        private $_modelUserVal;
        private $_mail;

        function DefaultAction() {
            require_once(DIR_VIEW."vSendMail.php");
        }

        function KeyAction($id_user, $key) {
            if(!is_numeric($id_user) || strlen($key) < 128)
                exit(header('Location: '.MVC_ROOT.'/Error/Error/404'));
            $this->id_user = $id_user;
            $this->val_key = $key;

            $this->_modelUserVal = new m\UserValidation();
            $this->_modelUserVal->id_user = $this->id_user;

            if(!($this->_modelUserVal->getKey())) // Unable to find key
                exit(header('Location: '.MVC_ROOT.'/Error/Error/404'));

            if($this->_modelUserVal->getKey() != $this->val_key) {
                // Different key, send a new mail ?
                $this->err_msg = $this->txt->Validate->message;
                require_once(DIR_VIEW."vValidate.php");
            }
            else {
                // Same keys, validate account
                $this->_modelUserVal->Delete();
                $_SESSION['id'] = $this->id_user;
                if(!empty($_SESSION['validate']))
                    unset($_SESSION['validate']);
				//all is good, his account is validated
				//now we disconnect him, he have to log in himself
                header('Location: '.MVC_ROOT.'Logout');
            }
        }

        function SendMailAction() {
        // Send AGAIN registration mail with validation key
            sleep(1000);
            if(!empty($_SESSION['id'])) {
                // If logged

                // One mail per minute
                $w = 0;
                if(!empty($_SESSION['sendMail'])) {
                    if($_SESSION['sendMail']+60 < time()) {
                        $w = 1;
                        $this->err_msg = $this->txt->Validate->wait;
                        require_once(DIR_VIEW."vValidate.php");
                    }
                }

                if($w == 0) {
                    // Allowed to send a new mail
                    $this->_modelUserVal = new m\UserValidation();
                    $this->_modelUserVal->id_user = $_SESSION['id'];
                    if(!($this->_modelUserVal->getKey()))
                        exit(header('Location: '.MVC_ROOT.'/Error/Error/404'));

                    $this->_modelUser = new m\Users();
                    $this->_modelUser->id = $_SESSION['id'];
                    if(!($user_mail = $this->_modelUser->getEmail()))
                       exit(header('Location: '.MVC_ROOT.'/Error/Error/404'));

                    $key = hash('sha512', uniqid(rand(), true));

                    $this->_modelUserVal->val_key = $key;
                    $this->_modelUserVal->Update();

                    $this->_mail = new l\Mail();
                    $this->_mail->_to = $user_mail;
                    $this->_mail->_subject = $this->txt->Register->subject;
                    $this->_mail->_message = str_replace("[id_user]", $_SESSION['id'], str_replace("[key]", $key, $this->txt->Register->message));
                    $this->_mail->send();
                    $_SESSION['sendMail'] = time();

                    $this->err_msg = $this->txt->Global->mail_sent;
                    require_once(DIR_VIEW."vValidate.php");
                }
            }
            else {
                // If not logged
                // Redirect to login page and after to this method again
                header('Location: '.MVC_ROOT.'/Login');
            }
        }
    };
?>
