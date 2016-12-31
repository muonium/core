<?php
namespace application\models;
use \library\MVC as l;

class Ban extends l\Model {
        
        /*
            1	id        int(11)			AUTO_INCREMENT	
            2	id_user   int(11)
            3	reason    varchar(128)	
            4	duration  int(11)	
        */
        
        protected $id;
        protected $id_user;
        protected $reason;
        protected $duration;
        
        /* ******************** SETTER ******************** */
        
        /* ******************** GETTER ******************** */
        function getId() {
            return $this->id;
        }
        
        function getIdUser() {
            return $this->id_user;
        }
        
        function getReason() {
           return $this->reason;
        }
        
        function getDuration() {
            return $this->duration;
        }

	function deleteBan() {
            if(!empty($this->id_user)) {
				
                if(is_numeric($this->id_user)) {
                    $req = self::$_sql->prepare("DELETE FROM ban WHERE id_user = ?");
                   return $req->execute(array($this->id_user));
                   
                }
            }
            return false;
        }
    }
