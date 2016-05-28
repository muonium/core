<?php
    class mStockage extends Model {
        
        private $id;
        private $Taille;
        private $Type;
        
        /* ******************** SETTER ******************** */
        function setId($id) {
            $this->id = $id;
        }
        
        function setTaille($t) {
            $this->Taille = $t;
        }
        
        
        function setType($t) {
            $this->Type = $t;
        }
        
        /* ******************** GETTER ******************** */
        function getId() {
            return $this->id;
        }
        
         function getTaille() {
            return $this->Taille;
        }
        
         function getType() {
           return $this->Type;
        }
    }
?>