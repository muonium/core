<?php
namespace application\controllers;
use \library\MVC as l;
use \application\models as m;

class Profile extends l\Languages
{
    private $_modelUser;
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
        /*
		- receive the new base64encoded encrypted CEK
		- store it in the database
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
};
?>
