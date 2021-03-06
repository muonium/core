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

    function __construct() {
        parent::__construct([
            'mustBeLogged' => true,
            'mustBeValidated' => true
        ]);
    }

    function DefaultAction() {
        $this->_modelUser = new m\Users($_SESSION['id']);
        require_once(DIR_VIEW."Profile.php");
    }

    function ChangeLoginAction() {
        // Called by profile.js
        if(!empty($_POST['login'])) {
            $login = urldecode($_POST['login']);

            if(preg_match("/^[A-Za-z0-9_.-]{2,19}$/", $login)) {
                $this->_modelUser = new m\Users($_SESSION['id']);
                $this->_modelUser->login = $login;

                if(!($this->_modelUser->LoginExists())) {
                    if($this->_modelUser->updateLogin()) {
						$_SESSION['login'] = $this->_modelUser->login;
                        echo 'ok@'.self::$txt->Profile->updateOk;
                    } else {
                        echo self::$txt->Profile->updateErr;
                    }
                } else {
                    echo self::$txt->Profile->loginExists;
                }
            } else {
                echo self::$txt->Register->loginFormat;
            }
        } else {
            echo self::$txt->Register->form;
        }
    }

	function ChangeMailAction() {
		// Called by profile.js
		if(!empty($_POST['mail'])) {
			$mail = urldecode($_POST['mail']);

			if(filter_var($mail, FILTER_VALIDATE_EMAIL)) {
				$this->_modelUser = new m\Users($_SESSION['id']);
				$this->_modelUser->email = $mail;

				if(!($this->_modelUser->EmailExists())) {
					if(!($this->_modelUser->LoginExists())) {
						if($this->_modelUser->updateMail()) {
							echo 'ok@'.self::$txt->Profile->updateOk;
						} else {
							echo self::$txt->Profile->updateErr;
						}
					} else {
						echo self::$txt->Profile->loginExists;
					}
				} else {
					echo self::$txt->Register->mailExists;
				}
			} else { // "mailFormat" response
				echo self::$txt->Profile->mailFormat;
			}
		} else {
			echo self::$txt->Profile->emptymail;
		}
	}

    function ChangePasswordAction() {
        // Called by profile.js
        if(!empty($_POST['old_pwd']) && !empty($_POST['new_pwd']) && !empty($_POST['pwd_confirm'])) {
            if($_POST['new_pwd'] === $_POST['pwd_confirm']) {
                $this->_modelUser = new m\Users($_SESSION['id']);

                if($user_pwd = $this->_modelUser->getPassword()) {
					$old_pwd = urldecode($_POST['old_pwd']);
                    if(password_verify($old_pwd, $user_pwd)) {
                        $this->_modelUser->password = password_hash(urldecode($_POST['new_pwd']), PASSWORD_BCRYPT);
                        if($this->_modelUser->updatePassword()) {
                            echo self::$txt->Profile->updateOk;
                        } else {
                            echo self::$txt->Profile->updateErr;
                        }
                    } else {
                        echo self::$txt->Profile->badOldPass;
                    }
                } else {
                    echo self::$txt->Profile->getpwd;
                }
            } else {
                echo self::$txt->Register->badPassConfirm;
            }
        } else {
            echo self::$txt->Register->form;
        }
    }

    function ChangeCekAction() {
        // Called by profile.js
        /*
		- receive the new base64encoded encrypted CEK
		- store it in the database
		- DO NOT FORGET: THE PASSPHRASE MUST NOT BE SENT TO THE SERVERS!!!!!
		- keep the cek as an urlencoded string, it's urldecoded at the frontend anyway
		*/
		if (!empty($_POST['cek'])) {
			$this->_modelUser = new m\Users($_SESSION['id']);
			$this->_modelUser->cek = $_POST['cek']; // set the 'cek' value for the MySQL request
			if ($this->_modelUser->updateCek()) { // try to update
				echo "ok@".self::$txt->Profile->updateOk; // all is okay, return that request went fine
			} else { // error, cannot update
				echo self::$txt->cek->updateErr;
			}
		} else { // CEK value was sent empty
			echo self::$txt->cek->empty;
		}
	}

    function ChangeAuthAction() {
        // Called by profile.js
        $this->_modelUser = new m\Users($_SESSION['id']);
        $s = 0;
        if($_POST['doubleAuth'] == 'true') $s = 1;

        if($this->_modelUser->updateDoubleAuth($s)) {
            echo self::$txt->Profile->updateOk;
        } else {
            echo self::$txt->Profile->updateErr;
		}
    }

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

		$this->_modelUser = new m\Users($_SESSION['id']);
        $this->_modelStorage = new m\Storage($_SESSION['id']);
        $this->_modelFiles = new m\Files($_SESSION['id']);
        $this->_modelFolders = new m\Folders($_SESSION['id']);
        $this->_modelBan = new m\Ban($_SESSION['id']);
        $this->_modelUserValidation = new m\UserValidation($_SESSION['id']);
        $this->_modelUserLostPass = new m\UserLostPass($_SESSION['id']);

		if(!($this->_modelUser->LoginExists())) {

			if($this->_modelUserLostPass->Delete()) {
			    if($this->_modelUserValidation->Delete()) {
			        if($this->_modelBan->deleteBan()) {
			            if($this->_modelFiles->deleteFilesfinal()) {
			                if($this->_modelFolders->deleteFoldersfinal()) {
			                    if($this->_modelStorage->deleteStorage()) {
									if($this->_modelUser->deleteUser()) {
										echo 'ok@'.self::$txt->Profile->accountDeletionOk;
										removeDirectory(NOVA.'/'.$_SESSION['id']);
										session_destroy();
									} else {
										echo self::$txt->Profile->updateErr;
									}
			                    } else {
			                        echo self::$txt->Profile->updateErr;
			                    }
			                } else {
			                    echo self::$txt->Profile->updateErr;
			                }
			            } else {
			            	echo self::$txt->Profile->updateErr;
			            }
			        } else {
			            echo self::$txt->Profile->updateErr;
			        }
			    } else {
			        echo self::$txt->Profile->updateErr;
			    }
			} else {
			    echo self::$txt->Profile->updateErr;
			}
		} else {
			echo self::$txt->Profile->loginExists;
		}
	}
};
