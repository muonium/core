<?php
namespace library\MVC;
use \config as conf;

// This class is called by all models (with "extends")
class Model {

    protected $_sql;
    //protected $_ListModel = array();
    //protected $_RequeteSql;

    function __construct() {
        $this->_sql = new \PDO('mysql:host='.conf\confDB::hostDefaut.';dbname='.conf\confDB::bddDefaut,conf\confDB::userDefaut,conf\confDB::passDefaut);
    }

    public static function getInstance() {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }
        return self::$instance;
    }

    function getLastInsertedId() {
        return $this->_sql->lastInsertId();
    }

    public function __get($attr) {
        return $this->$attr;
    }

    public function __set($attr, $val) {
        $this->$attr = $val;
    }
};
