<?php
    class mFiles extends Model {
        
        /*
            1   id                  int(11)     AUTO_INCREMENT
            2   id_owner            int(11)
            3	file                varchar(128)
            4	size	            int(11)
            5	last_modification	int(11)
            6   favorite            tinyint(1)
        */
        
        private $id;
        private $id_owner;
        private $file;
        private $size;
        private $last_modification;
        private $favorite;
        
        /* ******************** SETTER ******************** */
        function setIdOwner($id_owner) {
            $this->id_owner = $id_owner;
        }
        
        function setFile($file) {
            $this->file = $file;
        }
            
        function setSize($size) {
            $this->size = $size;
        }
        
        function setLastModification($last_modification) {
            $this->last_modification = $last_modification;
        }
        
        function setFavorite() {
            $this->favorite = 1;
        }
        
        function unsetFavorite() {
            $this->favorite = 0;
        }
        
        /* ******************** GETTER ******************** */
        function getId() {
            return $this->id;
        }
        
        function getIdOwner() {
            return $this->id_owner;
        }
        
        function getFile() {
            return $this->file;
        }
            
        function getSize() {
            return $this->size;
        }
        
        function getLastModification() {
            return $this->last_modification;
        }
        
        function getFavorite() {
            return $this->favorite;
        }
    }
?>