<?php
class Bug extends Languages {

    private $_modelUser;
    private $_Bruteforce;
    private $_mail;
    private $_message;
    
    // Values tables //
    
    // Different possible values for select tag
    
    private $values = array(
        "os" => array(
            "Linux" => "Linux/Unix/BSD",
            "Mac" => "Mac",
            "Win" => "Windows",
            "Android" => "Android",
            "iOS" => "iOS",
            "other" => ""          
        ),
        "browser" => array(
            "Chrome" => "Google Chrome/Chromium",
            "Firefox" => "Firefox",
            "Edge" => "Microsoft Edge",
            "Safari" => "Apple Safari",
            "Opera" => "Opera",
            "Explorer" => "Internet Explorer",
            "other" => ""
        )
    );
    
    ///////////////////
    
    function __construct() {
        parent::__construct();
        if(!isset($_SESSION['id']))
            exit(header('Location: '.MVC_ROOT.'/Error/Error/404'));
        // Initialize the anti-bruteforce class
        $this->_Bruteforce = new AntiBruteforce();
        $this->_Bruteforce->setFolder(ROOT.DS."tmp");
        $this->_Bruteforce->setSID('Bug');
        $this->_Bruteforce->setNbMaxAttemptsPerHour(20);
    }
    
    function printValues($key) {
        // Print values from values array for the selected key
        if(array_key_exists($key, $this->values)) {
            foreach($this->values[$key] as $key => $value) {
                if($key == 'other')
                    $value = $this->txt->Bug->other;
                echo '\n<option value="'.htmlentities($key).'">'.htmlentities($value).'</option>';
            }
        }
    }
    
    function checkValue($value, $key) {
        // Check if the entered value is in the array
        if(array_key_exists($value, $this->values[$key]))
            return htmlentities($this->values[$key][$value]);
        return false;
    }
    
    function FormAction() {
        if(!empty($_POST['os']) && !empty($_POST['browser']) && !empty($_POST['message'])) {
            // Sleep during 2s to avoid a big number of requests (bruteforce)
            sleep(2);
            $this->_Bruteforce->Control();
            if($this->_Bruteforce->getError() == 0) {
                if(strlen($_POST['message']) > 50) {
                    if(($os = $this->checkValue($_POST['os'], 'os')) && ($browser = $this->checkValue($_POST['browser'], 'browser'))) {
                        // get User's mail
                        
                        $this->_modelUser = new mUsers();
                        $this->_modelUser->setId($_SESSION['id']);
                        if($mail = $this->_modelUser->getEmail()) {
                        
                            $message = htmlentities($_POST['message']);
                            // Send the mail

                            $this->_mail = new Mail();
                            //$this->_mail->setTo("muonium@protonmail.ch");
                            $this->_mail->setTo("dylanclement7@gmail.com");
                            $this->_mail->setSubject("[Bug report] ".$mail." - ".substr($message, 0, 20));
                            $this->_mail->setMessage("====================<br />
                            <strong>User mail :</strong> ".$mail."<br />
                            <strong>User ID :</strong> ".$_SESSION['id']."<br />
                            <strong>O.S :</strong> ".$os."<br />
                            <strong>Browser :</strong> ".$browser."<br />
                            <strong>Browser version :</strong> ".htmlentities($_POST['browserVersion'])."
                            <br />====================<br />"
                                .nl2br($message)
                            );
                            $this->_mail->send();

                            $this->_message = $this->txt->Bug->sent;
                        }
                        else {
                            
                        }
                        
                    }
                    else {
                        $this->_message = $this->txt->Bug->form;
                    }
                }
                else {
                    $this->_message = $this->txt->Bug->messageLength;
                }
            }
            else {
                // Anti-bruteforce returns an error
                $this->_message = $this->txt->Register->{"bruteforceErr".$this->_Bruteforce->getError()};
            }
        }
        include_once(DIR_VIEW.'vBug.php');
    }

    function DefaultAction() {
        include_once(DIR_VIEW.'vBug.php');
    }
};
?>