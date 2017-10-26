<?php
namespace application\controllers;
use \library\MVC as l;
use \application\models as m;

class Bug extends l\Languages {

    private $_modelUser;
    private $_bruteforce;
    private $_mail;
    private $_message;

    // Different possible values for select tag

    private $values = [
        "os" => [
            "Linux" => "Linux/Unix/BSD",
            "Mac" => "Mac",
            "Win" => "Windows",
            "Android" => "Android",
            "iOS" => "iOS",
            "other" => ""
        ],
        "browser" => [
            "Chrome" => "Google Chrome/Chromium",
            "Firefox" => "Firefox",
            "Edge" => "Microsoft Edge",
            "Safari" => "Apple Safari",
            "Opera" => "Opera",
            "Explorer" => "Internet Explorer",
            "other" => ""
        ]
    ];

    ///////////////////

    function __construct() {
        parent::__construct([
            'mustBeLogged' => true,
            'mustBeValidated' => false
        ]);

        // Initialize the anti-bruteforce class
        $this->_bruteforce = new l\AntiBruteforce();
        $this->_bruteforce->setFolder(ROOT.DS."tmp");
        $this->_bruteforce->setSID('Bug');
        $this->_bruteforce->setNbMaxAttemptsPerHour(20);
    }

    function printValues($key) {
        // Print values from values array for the selected key
        if(array_key_exists($key, $this->values)) {
            foreach($this->values[$key] as $key => $value) {
                if($key == 'other') $value = $this->txt->Bug->other;
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
            $this->_bruteforce->Control();
            if($this->_bruteforce->getError() == 0) {
                if(strlen($_POST['message']) > 50) {
                    if(($os = $this->checkValue($_POST['os'], 'os')) && ($browser = $this->checkValue($_POST['browser'], 'browser'))) {
                        // get User's mail

                        $this->_modelUser = new m\Users($_SESSION['id']);
                        if($mail = $this->_modelUser->getEmail()) {

                            $message = htmlentities($_POST['message']);
                            // Send the mail

                            $this->_mail = new l\Mail();
                            $this->_mail->_to = "bug@muonium.ee";
                            $this->_mail->_subject = "[Bug report] ".$mail." - ".substr($message, 0, 20);
                            $this->_mail->_message = "====================<br />
                            <strong>User mail :</strong> ".$mail."<br />
                            <strong>User ID :</strong> ".$_SESSION['id']."<br />
                            <strong>O.S :</strong> ".$os."<br />
                            <strong>Browser :</strong> ".$browser."<br />
                            <strong>Browser version :</strong> ".htmlentities($_POST['browserVersion'])."
                            <br />====================<br />"
                                .nl2br($message);
                            $this->_mail->send();

                            $this->_message = $this->txt->Bug->sent;
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
                $this->_message = $this->txt->Register->{"bruteforceErr".$this->_bruteforce->getError()};
            }
        }
        require_once(DIR_VIEW.'Bug.php');
    }

    function DefaultAction() {
        require_once(DIR_VIEW.'Bug.php');
    }
};
?>
