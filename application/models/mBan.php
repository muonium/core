<?php
    class mBan extends Model {
        
        /*
            1	id        int(11)			AUTO_INCREMENT	
            2	id_user   int(11)
            3	reason    varchar(128)	
            4	duration  int(11)	
        */
        
        private $id;
        private $id_user;
        private $reason;
        private $duration;
        
        /* ******************** SETTER ******************** */
        function setIdUser($id_user) {
            $this->id_user = $id_user;
        }
        
        function setReason($reason) {
            $this->reason = $reason;
        }
        
        function setDuration($duration) {
            $this->duration = $duration;
        }
        
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
    }
