<?php
namespace application\controllers;
use \library\MVC as l;
use \application\models as m;

class Profile extends l\Languages
{
    private $_modelUser;
	private $_modelBan;
	private $_modelFiles;
	private $_modelStorage;
	private $_modelFolders;
	private $_modelUserLostPass;
	private $_modelUserValidation;

    private $ppCounter = 0;

    function __construct() {
        parent::__construct();
        if(empty($_SESSION['id']))
            exit(header('Location: '.MVC_ROOT.'/Error/Error/404'));
        if(!empty($_SESSION['validate']))
            exit(header('Location: '.MVC_ROOT.'/Validate'));
    }

    function DefaultAction() {
        $this->_modelUser = new m\Users();
        $this->_modelUser->id = $_SESSION['id'];
        $this->ppCounter = $this->_modelUser->getPpCounter();
        require_once(DIR_VIEW."vProfile.php");
    }

    function ChangeLoginAction() {
        // Called by profile.js

        if(!empty($_POST['login'])) {
            $login = urldecode($_POST['login']);

            if(preg_match("/^[A-Za-z0-9_.-]{2,19}$/", $login)) {
                $this->_modelUser = new m\Users();

                $this->_modelUser->id = $_SESSION['id'];
                $this->_modelUser->login = $_POST['login'];

                if(!($this->_modelUser->LoginExists())) {
                    if($this->_modelUser->updateLogin()) {
                        echo 'ok@'.$this->txt->Profile->updateOk;
                    }
                    else {
                        echo $this->txt->Profile->updateErr;
                    }
                }
                else {
                    echo $this->txt->Profile->loginExists;
                }
            }
            else {
                echo $this->txt->Register->loginFormat;
            }
        }
        else {
            echo $this->txt->Register->form;
        }
    }

    function ChangePasswordAction() {
        // Called by profile.js

        if(!empty($_POST['old_pwd']) && !empty($_POST['new_pwd']) && !empty($_POST['pwd_confirm'])) {
            if($_POST['new_pwd'] == $_POST['pwd_confirm']) {
                        $this->_modelUser = new m\Users();

                        $this->_modelUser->id = $_SESSION['id'];
                        if($user_pwd = $this->_modelUser->getPassword()) {
                            if($user_pwd == $_POST['old_pwd']) {
                                $this->_modelUser->password = $_POST['new_pwd'];
                                if($this->_modelUser->updatePassword()) {
                                    echo 'ok@'.$this->txt->Profile->updateOk;
                                }
                                else {
                                    echo $this->txt->Profile->updateErr;
                                }
                            }
                            else {
                                echo $this->txt->Register->badOldPass;
                            }
                        }
                        else {
                            echo $this->txt->Profile->getpwd;
                        }
            }
            else {
                echo $this->txt->Register->badPassConfirm;
            }
        }
        else {
            echo $this->txt->Register->form;
        }
    }

    function ChangePassPhraseAction() {
        // Called by profile.js

        echo $this->txt->Error->pp;
        /*
        if(!empty($_POST['old_pp']) && !empty($_POST['new_pp']) && !empty($_POST['pp_confirm'])) {
            if($_POST['new_pp'] == $_POST['pp_confirm']) {
                        $this->_modelUser = new m\Users();

                        $this->_modelUser->id = $_SESSION['id'];
                        if($this->_modelUser->getPpCounter() < 2) {
                            if($user_pp = $this->_modelUser->getPassphrase()) {
                                    if($user_pp == $_POST['old_pp']) {
                                        $this->_modelUser->passphrase = $_POST['new_pp'];
                                        if($this->_modelUser->updatePassphrase()) {
                                            $this->_modelUser->incrementPpCounter();
                                            echo 'ok@'.$this->txt->Profile->updateOk;
                                        }
                                        else {
                                            echo $this->txt->Profile->updateErr;
                                        }
                                    }
                                    else {
                                        echo $this->txt->Register->badOldPassphrase;
                                    }
                            }
                            else {
                                echo $this->txt->Profile->getpp;
                            }
                        }
                }
                else {
                    echo $this->txt->Register->badPassphraseConfirm;
                }
            }
            else {
                echo $this->txt->Register->form;
            }
        */
    }

    function ChangeAuthAction() {
        // Called by profile.js

        $this->_modelUser = new m\Users();
        $this->_modelUser->id = $_SESSION['id'];

        $s = 0;
        if($_POST['doubleAuth'] == 'true')
            $s = 1;

        if($this->_modelUser->updateDoubleAuth($s))
            echo $this->txt->Profile->updateOk;
        else
            echo $this->txt->Profile->updateErr;
    }

/*     add function to change email of user  */

 function ChangeMailAction() {
        // Called by profile.js

        if(!empty($_POST['changemail'])) {


            if(filter_var($_POST['changemail'], FILTER_VALIDATE_EMAIL))
               {
                $this->_modelUser = new m\Users();

                $this->_modelUser->id = $_SESSION['id'];
                $this->_modelUser->email = $_POST['changemail'];

                if(!($this->_modelUser->LoginExists())) {
                    if($this->_modelUser->updateMail()) {
                        echo $this->txt->Profile->updateOk;
                    }
                    else {
                        echo $this->txt->Profile->updateErr;
                    }
                }
                else {
                    echo $this->txt->Profile->loginExists;
                }
             }
				else {
					// "mailFormat" response
					echo htmlentities($this->txt->Profile->mailFormat);
				}
        }
        else {
            echo $this->txt->Profile->emptymail;
        }
    }

/*                                               */

		/*   Add delete user function */

	function DeleteUserAction() {
		// Called by profile.js

		// function of remove the user directory and it's files
		function removeDirectory($path) {
			$files = glob($path . '/*');
			foreach ($files as $file) {
				is_dir($file) ? removeDirectory($file) : unlink($file);
			}
			rmdir($path);
			return;
		}

		$this->_modelUser = new m\Users();
		$this->_modelUser->id = $_SESSION['id'];
        $this->_modelStorage = new m\Storage();
        $this->_modelStorage->id_user = $_SESSION['id'];
        $this->_modelFiles = new m\Files();
        $this->_modelFiles->id_owner = $_SESSION['id'];
        $this->_modelFolders = new m\Folders();
        $this->_modelFolders->id_owner = $_SESSION['id'];
        $this->_modelBan = new m\Ban();
        $this->_modelBan->id_user = $_SESSION['id'];
        $this->_modelUserValidation = new m\UserValidation();
        $this->_modelUserValidation->id_user = $_SESSION['id'];
        $this->_modelUserLostPass = new m\UserLostPass();
        $this->_modelUserLostPass->id_user = $_SESSION['id'];

		if(!($this->_modelUser->LoginExists())) {

			if($this->_modelUserLostPass->Delete()) {
			    if($this->_modelUserValidation->Delete()) {
			        if($this->_modelBan->deleteBan()) {
			            if($this->_modelFiles->deleteFilesfinal()) {
			                if($this->_modelFolders->deleteFoldersfinal()) {
			                    if($this->_modelStorage->deleteStorage()) {
									if($this->_modelUser->deleteUser()) {
										echo 'ok@'.$this->txt->Profile->accountDeletionOk;
										removeDirectory(NOVA.'/'.$_SESSION['id']);

										session_destroy();
									}
									else {
										echo $this->txt->Profile->updateErr;
									}
			                    }
			                    else {
			                        echo $this->txt->Profile->updateErr;
			                    }
			                }
			            	else {
			                    echo $this->txt->Profile->updateErr;
			                }
			            }
			            else {
			            	echo $this->txt->Profile->updateErr;
			            }
			        }
			        else {
			            echo $this->txt->Profile->updateErr;
			        }
			    }
			    else {
			        echo $this->txt->Profile->updateErr;
			    }
			}
			else {
			    echo $this->txt->Profile->updateErr;
			}
		}
		else {
			echo $this->txt->Profile->loginExists;
		}
	}

/*                             */
};
?>
