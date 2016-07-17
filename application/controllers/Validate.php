<?php
    class Validate extends Languages {
        
        private $id_user;
        private $val_key;
        private $err_msg;
        
        private $_modelUser;
        private $_modelUserVal;
        private $_mail;
        
        function DefaultAction() {
            include_once(DIR_VIEW."vSendMail.php");
        }
        
        function KeyAction($id_user, $key) {
            if(!is_numeric($id_user) || strlen($key) < 128)
                exit(header('Location: '.MVC_ROOT.'/Error/Error/404'));
            $this->id_user = $id_user;
            $this->val_key = $key;
            
            $this->_modelUserVal = new mUserValidation();
            $this->_modelUserVal->setIdUser($this->id_user);
            
            if(!($this->_modelUserVal->getKey())) // Unable to find key
                exit(header('Location: '.MVC_ROOT.'/Error/Error/404'));
            
            if($this->_modelUserVal->getKey() != $this->val_key) {
                // Different key, send a new mail ?
                $this->err_msg = $this->txt->Validate->message;
                include_once(DIR_VIEW."vValidate.php");
            }
            else {
                // Same keys, validate account
                $this->_modelUserVal->Delete();
                $_SESSION['id'] = $this->id_user;
                if(!empty($_SESSION['validate']))
                    unset($_SESSION['validate']);
                header('Location: '.MVC_ROOT);
            }
        }
        
        function sendMailAction() {
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
                        include_once(DIR_VIEW."vValidate.php");
                    }
                }

                if($w == 0) {
                    // Allowed to send a new mail
                    $this->_modelUserVal = new mUserValidation();
                    $this->_modelUserVal->setIdUser($_SESSION['id']);
                    if(!($this->_modelUserVal->getKey()))
                        exit(header('Location: '.MVC_ROOT.'/Error/Error/404'));

                    $this->_modelUser = new mUsers();
                    $this->_modelUser->setId($_SESSION['id']);
                    if(!($user_mail = $this->_modelUser->getEmail()))
                       exit(header('Location: '.MVC_ROOT.'/Error/Error/404'));

                    $key = hash('sha512', uniqid(rand(), true));

                    $this->_modelUserVal->setKey($key);
                    $this->_modelUserVal->Update();

                    $this->_mail = new Mail();
                    $this->_mail->setTo($user_mail);
                    $this->_mail->setSubject($this->txt->Register->subject);
                    $this->_mail->setMessage(str_replace("[id_user]", $_SESSION['id'], str_replace("[key]", $key, $this->txt->Register->message)));
                    $this->_mail->send();
                    $_SESSION['sendMail'] = time();
                    
                    $this->err_msg = $this->txt->Global->mail_sent;
                    include_once(DIR_VIEW."vValidate.php");
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