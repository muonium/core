<?php
class Bug extends Languages {

    private $_modelUser;
    private $_modelUserVal;
    private $_Bruteforce;
    private $_mail;
    
    function __construct() {
        parent::__construct();
        if(!empty($_SESSION['id']))
            exit(header('Location: '.MVC_ROOT.'/Error/Error/404'));
        // Initialize the anti-bruteforce class
        $this->_Bruteforce = new AntiBruteforce();
        $this->_Bruteforce->setFolder(ROOT.DS."tmp");
        $this->_Bruteforce->setSID('Bug');
        $this->_Bruteforce->setNbMaxAttemptsPerHour(20);
    }
    
    function FormAction() {
        // Sleep during 2s to avoid a big number of requests (bruteforce)
        sleep(2);
        $this->_Bruteforce->Control();
        if($this->_Bruteforce->getError() == 0)
        {
        }
        else {
            // Anti-bruteforce returns an error
            echo htmlentities($this->txt->Register->{"bruteforceErr".$this->_Bruteforce->getError()});
        }
    }

    function DefaultAction() {
        include_once(DIR_VIEW.'vBug.php');
    }
};
?>