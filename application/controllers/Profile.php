<?php
class Profile extends Languages
{
    private $_modelUser;
    
    function __construct() {
        parent::__construct();
        if(empty($_SESSION['id']))
            header('Location: '.MVC_ROOT.'/Error/Error/404');
        if(!empty($_SESSION['validate']))
            header('Location: '.MVC_ROOT.'/Validate');
    }
    
    function DefaultAction() {
        include(DIR_VIEW."vProfile.php");
    }
    
    function changeLoginAction() {
        // Called by profile.js
        
        if(!empty($_POST['login'])) {
            $login = urldecode($_POST['login']);
            
            if(preg_match("/^[A-Za-z0-9_.-]{2,19}$/", $login)) {
                $this->_modelUser = new mUsers();

                $this->_modelUser->setId($_SESSION['id']);
                $this->_modelUser->setLogin($_POST['login']);
                
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
    
    function changePasswordAction() {
        // Called by profile.js
        
        if(!empty($_POST['old_pwd']) && !empty($_POST['new_pwd']) && !empty($_POST['pwd_confirm'])) {
            if($_POST['new_pwd'] == $_POST['pwd_confirm']) {
                if(is_numeric($_POST['pwd_length'])) {
                    if($_POST['pwd_length']) {
                        $this->_modelUser = new mUsers();

                        $this->_modelUser->setId($_SESSION['id']);
                        if($user_pwd = $this->_modelUser->getPassword()) {
                            if($user_pwd == $_POST['old_pwd']) {
                                $this->_modelUser->setPassword($_POST['new_pwd']);
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
                        echo $this->txt->Register->passLength;
                    }
                }
                else {
                    echo $this->txt->Error->form;
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
    
    function changePassPhraseAction() {
        // Called by profile.js
        
        echo $this->txt->Error->pp;
        /*if(!empty($_POST['old_pp']) && !empty($_POST['new_pp']) && !empty($_POST['pp_confirm'])) {
            if($_POST['new_pp'] == $_POST['pp_confirm']) {
                if(is_numeric($_POST['pp_length'])) {
                    if($_POST['pp_length']) {
                        $this->_modelUser = new mUsers();

                        $this->_modelUser->setId($_SESSION['id']);
                        if($user_pp = $this->_modelUser->getPassphrase()) {
                            if($user_pp == $_POST['old_pp']) {
                                $this->_modelUser->setPassphrase($_POST['new_pp']);
                                if($this->_modelUser->updatePassphrase()) {
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
                    else {
                        echo $this->txt->Register->passLength;
                    }
                }
                else {
                    echo $this->txt->Error->form;
                }
            }
            else {
                echo $this->txt->Register->badPassphraseConfirm;
            }
        }
        else {
            echo $this->txt->Register->form;
        }*/
    }
};
?>