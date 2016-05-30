<?php
// This class is called by all models (with "extends")
    class Model {
        
        protected $_InstancePDO;
        protected $_ListModel = array();
        protected $_RequeteSql;
        
        function __construct() {
            $_InstancePDO = new PDO('mysql:host='.confBDD::hostDefaut.';dbname='.confBDD::bddDefaut,confBDD::userDefaut,confBDD::passDefaut);
        }
	
	   public static function getInstance() {
           if (!isset(self::$instance)) {
               $c = __CLASS__;
               self::$instance = new $c;
           }
           return self::$instance;
       }
    }
?>