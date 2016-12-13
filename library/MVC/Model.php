<?php
namespace library\MVC;
use \config as conf;

// This class is called by all models (with "extends")
class Model {

    protected static $_sql;

    function __construct() {
        self::$_sql = new \PDO('mysql:host='.conf\confDB::host.';dbname='.conf\confDB::db,conf\confDB::user,conf\confDB::password);
        self::$_sql->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        self::$_sql->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
    }

    public static function getInstance() {
        if (!isset(self::$_sql)) {
            $c = __CLASS__;
            self::$_sql = new $c;
        }
        return self::$_sql;
    }

    function getLastInsertedId() {
        return self::$_sql->lastInsertId();
    }

    public function __get($attr) {
        return $this->$attr;
    }

    public function __set($attr, $val) {
        $this->$attr = $val;
    }
};
