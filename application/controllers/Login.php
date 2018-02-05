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
            $this->_message = htmlentities(self::$txt->Login->expired);
            require_once(DIR_VIEW."Login.php");
        }

        elseif(strlen($_POST['code']) != 8) {
            $this->_message = htmlentities(self::$txt->Login->invalidCode);
            require_once(DIR_VIEW."DoubleAuth.php");
        }
        else {
            $brute = new l\AntiBruteforce();
            $brute->setFolder(ROOT.DS."tmp");
            $brute->setNbMaxAttemptsPerHour(50);

            $user = new m\Users($_SESSION['tmp_id']);

            if($user->getDoubleAuth()) {
                if($code = $user->getCode()) {
                    if($code == $_POST['code']) {
                        // Code is correct
                        $_SESSION['id'] = $_SESSION['tmp_id'];
						$_SESSION['login'] = $user->getLogin();
                        unset($_SESSION['tmp_id']);
                        exit(header('Location: '.MVC_ROOT.'/User'));
                    }
                    else {
                        // Code is wrong
                        $brute->setSID('doubleAuth');
                        $brute->Control();
                        $this->_message = htmlentities(self::$txt->Login->invalidCode).'<br>'.htmlentities(self::$txt->Register->{"bruteforceErr".$brute->getError()});
                        require_once(DIR_VIEW."DoubleAuth.php");
                    }
                }
                else { // Unable to get code
                    exit(header('Location: '.MVC_ROOT.'/Logout'));
				}
            }
            else { // Double auth disabled
                exit(header('Location: '.MVC_ROOT.'/Logout'));
			}
        }
    }

    function ConnectionAction() {
        // Sleep during 3s to avoid a big number of requests (bruteforce)
        sleep(2);
        if(!empty($_POST['username']) && !empty($_POST['pass'])) {
            $new_user = new m\Users();

            if(filter_var($_POST['username'], FILTER_VALIDATE_EMAIL) === false){
                $new_user->login = urldecode($_POST['username']);
				$e = $new_user->getEmail();
            } else {
                $new_user->email = urldecode($_POST['username']);
				$e = $_POST['username'];
			}

            $new_user->password = urldecode($_POST['pass']);
            $brute = new l\AntiBruteforce();
            $brute->setFolder(ROOT.DS."tmp");
            $brute->setNbMaxAttemptsPerHour(50);
            if(!($id = $new_user->getId())) {
                // User doesn't exists - Anti bruteforce with session id
                $brute->setSID();
                $brute->Control();
                echo htmlentities(self::$txt->Login->{"bruteforceErr".$brute->getError()});
            }
            else {
                $new_user->id = $id;
				$login = $new_user->getLogin();
                $pass = $new_user->getPassword();
				$cek = $new_user->getCek();

                if($pass !== false) {
                    if(password_verify($new_user->password, $pass)) {
                        // Mail, password ok, connection
						$new_user->updateLastConnection();
                        $mUserVal = new m\UserValidation($id);
                        if(!($mUserVal->getKey())) {
                            // Unable to find key - Validation is done
                            if($new_user->getDoubleAuth()) {
                                // Double auth
                                $_SESSION['tmp_id'] = $id;
                                // Send an email with a code
                                $code = $this->generateCode();
                                $new_user->updateCode($code);
                                $mail = new l\Mail();
                                $mail->_to = $e;
                                $mail->_subject = "Muonium - ".self::$txt->Profile->doubleAuth;
                                $mail->_message = str_replace("[key]", $code, self::$txt->Login->doubleAuthMessage);
                                $mail->send();
                            }
                            else { // Logged
                                $_SESSION['id'] = $id;
								$_SESSION['login'] = $login;
							}
                            echo 'ok@'.$cek; //the CEK is already url encoded in the database
                        }
                        else {
                            // Key found - User needs to validate its account (double auth only for validated accounts)
                            $_SESSION['id'] = $id;
							$_SESSION['login'] = $login;
                            $_SESSION['validate'] = 1;
                            echo 'va@';
                        }
                        return;
                    }
                }

                // User exists but incorrect password - Anti bruteforce with user id
                $brute->setId($id);
                $brute->Control();
                echo htmlentities(self::$txt->Login->{"bruteforceErr".$brute->getError()});
            }
        }
        else {
            echo htmlentities(self::$txt->Register->form);
        }
    }
    function DefaultAction() {
        if(!empty($_SESSION['id'])) exit(header('Location: '.MVC_ROOT.'/Error/Error/404'));

        if(!empty($_SESSION['tmp_id'])) {
            // Double auth
            $this->_message = str_replace("[url_app]", URL_APP, self::$txt->Login->doubleAuth);
            require_once(DIR_VIEW."DoubleAuth.php");
        }
        else {
            require_once(DIR_VIEW."Login.php");
		}
    }

    function generateCode() {
        $code = '';
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        for($i = 0; $i < 8; $i++) {
            $code .= $chars[rand(0, strlen($chars)-1)];
        }
        return $code;
    }
};
?>
