<?php
    class mStorage extends Model {
        
        /*
            1   id              int(11)     AUTO_INCREMENT
            2   id_user         int(11)
            3   user_quota      int(11)
            4   size_stored     int(11)	
        */
        
        private $id;
        private $id_user;
        private $user_quota;
        private $size_stored;
        
        /* ******************** SETTER ******************** */
        function setIdUser($id_user) {
            $this->id_user = $id_user;
        }
        
        function setUserQuota($user_quota) {
            $this->user_quota = $user_quota;
        }       
        
        function setSizeStored($size_stored) {
            $this->size_stored = $size_stored;
        }
        
        /* ******************** GETTER ******************** */
        function getId() {
            return $this->id;
        }
        
        function getIdUser() {
            return $this->id_user;
        }
        
        function getUserQuota() {
            return $this->user_quota;
        }       
        
        function getSizeStored() {
            return $this->size_stored;
        }
    }
?>