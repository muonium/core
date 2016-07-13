<?php
	class Login extends Languages {
        
        function connectionAction() {
            // Sleep during 3s to avoid a big number of requests (bruteforce)
            sleep(3);
            
            if(!empty($_POST['mail']) && !empty($_POST['pass']) && !empty($_POST['passphrase'])) {
                if($_POST['passlength'])
                {
                    $newUser = new mUsers();
                    $newUser->setEmail(urldecode($_POST['mail']));
                    $newUser->setPassword($_POST['pass']);
                    $newUser->setPassphrase(urldecode($_POST['passphrase']));
                    //$newUser->setPassphrase($_POST['passphrase']);

                    //echo $_POST['pass'].'<br />'.$_POST['passphrase'].'<br />';
                    
                    $brute = new AntiBruteforce();
                    $brute->setFolder(ROOT.DS."tmp");
                    $brute->setNbMaxAttemptsPerHour(50);

                    if(!($id = $newUser->getId())) {
                        // User doesn't exists - Anti bruteforce with session id
                        $brute->setSID();
                        $brute->Control();
                        echo htmlentities($this->txt->Login->{"bruteforceErr".$brute->getError()});
                    }
                    else {
                        if(!($newUser->Connection())) {
                            // User exists - Anti bruteforce with user id
                            $brute->setId($id);
                            $brute->Control();
                            echo htmlentities($this->txt->Login->{"bruteforceErr".$brute->getError()});
                        }
                        else {
                            // Connection
                            $_SESSION['id'] = $id;

                            $mUserVal = new mUserValidation();
                            $mUserVal->setIdUser($id);

                            if(!($mUserVal->getKey())) // Unable to find key - Validation is done
                                echo 'ok@';
                            else {
                                $_SESSION['validate'] = 1;
                                echo 'va@';
                            }
                        }
                    }
                }
                else {
                    echo htmlentities($this->txt->Register->form);
                }
			}
            else {
                echo htmlentities($this->txt->Register->form);
            }
        }

		function DefaultAction() {
            if(!empty($_SESSION['id']))
                exit(header('Location: '.MVC_ROOT.'/Error/Error/404'));
            include_once(DIR_VIEW."vLogin.php");
		}
	};
?>
