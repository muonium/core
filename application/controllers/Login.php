<?php
namespace application\controllers;
use \library\MVC as l;
use \application\models as m;
class Login extends l\Languages {
    private $_message;
    
    function AuthCodeAction() {
        // User sent an auth code
        sleep(1);
        if(!isset($_SESSION['tmp_id'])) {
            $this->_message = htmlentities($this->txt->Login->expired);
            require_once(DIR_VIEW."vLogin.php");
        }
            
        elseif(strlen($_POST['code']) != 8) {
            $this->_message = htmlentities($this->txt->Login->invalidCode);
            require_once(DIR_VIEW."vDoubleAuth.php");
        }
        else {
            $brute = new l\AntiBruteforce();
            $brute->setFolder(ROOT.DS."tmp");
            $brute->setNbMaxAttemptsPerHour(50);
            
            $user = new m\Users();
            $user->id = $_SESSION['tmp_id'];
            
            if($user->getDoubleAuth()) {
                if($code = $user->getCode()) {
                    if($code == $_POST['code']) {
                        // Code is correct
                        $_SESSION['id'] = $_SESSION['tmp_id'];
                        unset($_SESSION['tmp_id']);
                        exit(header('Location: '.MVC_ROOT.'/User'));
                    }
                    else {
                        // Code is wrong
                        $brute->setSID('doubleAuth');
                        $brute->Control();
                        $this->_message = htmlentities($this->txt->Login->invalidCode).'<br />'.htmlentities($this->txt->Register->{"bruteforceErr".$brute->getError()});
                        require_once(DIR_VIEW."vDoubleAuth.php");
                    }
                }
                else // Unable to get code
                    exit(header('Location: '.MVC_ROOT.'/Logout'));
            }
            else // Double auth disabled
                exit(header('Location: '.MVC_ROOT.'/Logout'));
        }
    }
    
    function ConnectionAction() {
        // Sleep during 3s to avoid a big number of requests (bruteforce)
        sleep(2);
        if(!empty($_POST['username']) && !empty($_POST['pass']) && !empty($_POST['passphrase'])) {
            $new_user = new m\Users();
            
            if(filter_var($_POST['username'], FILTER_VALIDATE_EMAIL) === false)
                $new_user->login = urldecode($_POST['username']);
            else
                $new_user->email = urldecode($_POST['username']);
            
            $new_user->password = urldecode($_POST['pass']);
            $new_user->passphrase = urldecode($_POST['passphrase']);
            $brute = new l\AntiBruteforce();
            $brute->setFolder(ROOT.DS."tmp");
            $brute->setNbMaxAttemptsPerHour(50);
            if(!($id = $new_user->getId())) {
                // User doesn't exists - Anti bruteforce with session id
                $brute->setSID();
                $brute->Control();
                echo htmlentities($this->txt->Login->{"bruteforceErr".$brute->getError()});
            }
            else {
                $new_user->id = $id;
                $pass = $new_user->getPassword();
                $pp = $new_user->getPassphrase();
                
                if($pass !== false && $pp !== false) {
                    //if(password_verify($new_user->password, $pass) && password_verify($new_user->passphrase, $pp)) {
                    if(password_verify($new_user->password, $pass) && $new_user->passphrase == $pp) {
                        // Mail, password and passphrase ok, connection
                        $mUserVal = new m\UserValidation();
                        $mUserVal->id_user = $id;
                        if(!($mUserVal->getKey())) { 
                            // Unable to find key - Validation is done
                            if($new_user->getDoubleAuth()) {
                                // Double auth
                                $_SESSION['tmp_id'] = $id;
                                // Send an email with a code
                                $code = $this->generateCode();
                                $new_user->updateCode($code);
                                $mail = new l\Mail();
                                $mail->_to = $_POST['mail'];
                                $mail->_subject = "Muonium - ".$this->txt->Profile->doubleAuth;
                                $mail->_message = str_replace("[key]", $code, $this->txt->Login->doubleAuthMessage);
                                $mail->send();
                            }
                            else // Logged
                                $_SESSION['id'] = $id;
                            echo 'ok@';
                        }
                        else {
                            // Key found - User needs to validate its account (double auth only for validated accounts)
                            $_SESSION['id'] = $id;
                            $_SESSION['validate'] = 1;
                            echo 'va@';
                        }
                        return;
                    }
                }
                
                // User exists but incorrect password/passphrase - Anti bruteforce with user id
                $brute->setId($id);
                $brute->Control();
                echo htmlentities($this->txt->Login->{"bruteforceErr".$brute->getError()});
            }
        }
        else {
            echo htmlentities($this->txt->Register->form);
        }
    }
    function DefaultAction() {
        if(!empty($_SESSION['id']))
            exit(header('Location: '.MVC_ROOT.'/Error/Error/404'));
        elseif(!empty($_SESSION['tmp_id'])) {
            // Double auth
            $this->_message = $this->txt->Login->doubleAuth;
            require_once(DIR_VIEW."vDoubleAuth.php");
        }
        else
            require_once(DIR_VIEW."vLogin.php");
    }
    
    function generateCode() {
        $code = '';
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        for($i=0;$i<8;$i++) {
            $code .= $chars[rand(0, strlen($chars)-1)];
        }
        return $code;
    }
};
?>
