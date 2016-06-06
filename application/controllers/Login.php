<?php
	class Login extends Languages {
        
        private $_error = '';

		function DefaultAction() {

			if(!empty($_POST['mail']) && !empty($_POST['pass']) && !empty($_POST['passphrase'])) {
                sleep(3);
				$newUser = new mUsers();
                $newUser->setEmail($_POST['mail']);
                $newUser->setPassword(hash('sha512', $_POST['pass']));
                $newUser->setPassphrase(hash('sha512', $_POST['passphrase']));
                
                $brute = new AntiBruteforce();
                $brute->setFolder(ROOT.DS."tmp");
                $brute->setNbMaxAttemptsPerHour(50);
                
                if(!($id = $newUser->getId())) {
                    // User doesn't exists - Anti bruteforce with session id
                    $brute->setSID();
                    $brute->Control();
                    $this->_error = $this->txt->Login->{"bruteforceErr".$brute->getError()};
                }
                else {
                    if(!($newUser->Connection())) {
                        // User exists - Anti bruteforce with user id
                        $brute->setId($id);
                        $brute->Control();
                        $this->_error = $this->txt->Login->{"bruteforceErr".$brute->getError()};
                    }
                    else {
                        // Connection
                        $_SESSION['id'] = $id;
                        
                        $mUserVal = new mUserValidation();
                        $mUserVal->setIdUser($id);

                        if(!($mUserVal->getKey())) // Unable to find key - Validation is done
                            header('Location: '.MVC_ROOT);
                        else {
                            $_SESSION['validate'] = 1;
                            header('Location: '.MVC_ROOT.'/Validate');
                        }
                    }
                }
			}
            
            include_once(DIR_VIEW."vLogin.php");
		}
	};
?>
