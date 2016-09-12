<?php
class AntiBruteforce
{
    private $_id;
    private $_sid;
    
    private $_tmpFile;
    private $_error = 0;
    
    // Folder to store temporary files
    private $_folder = 'tmp';
    
    private $_prefixe = '';
    
    private $_NbMaxAttemptsPerHour = 15; 
    
    function setId($id, $prefixe = '') {
        $this->_prefixe = $prefixe;
        $this->_id = $id;
        // Temporary file with the number of connections/requests for the user, username is encrypted with sha1 and a salt for more security.
        $this->_tmpFile = $this->_folder.'/'.$prefixe.sha1($id.'c4$AZ_').'.tmp';
    }
    
    function setSID($prefixe = '') {
        $this->_prefixe = $prefixe;
        $this->_sid = session_id();
        // Temporary file with the number of connections/requests for the session id, session id is encrypted with sha1 and a salt for more security.
        $this->_tmpFile = $this->_folder.'/'.$prefixe.sha1(session_id().'c4$AZ_').'.tmp';
    }
    
    function setFolder($folder) {
        $this->_folder = $folder;
    }
    
    function setNbMaxAttemptsPerHour($NbMaxAttemptsPerHour) {
        $this->_NbMaxAttemptsPerHour = $NbMaxAttemptsPerHour;
    }
    
    function getError() {
        return $this->_error;
    }
    
    function getNbMaxAttemptsPerHour() {
        return $this->_NbMaxAttemptsPerHour;
    }
    
    function banSID() {
        unlink($this->_folder.'/'.$this->_prefixe.sha1(session_id().'c4$AZ_').'.tmp');
        $_SESSION['banSID'] = 1;
    }
    
    function Control() {
        // This function increments the connection counter or reset it, and possibly launch a warning/ban
        
        $this->_error = 0;
        if(!file_exists($this->_tmpFile)) {
            // If the file doesn't exists, we create it
            $file = fopen($this->_tmpFile, "w");
            fwrite($file, time().';1');
        }
        else {
            // File exists
            $fileContent = file_get_contents($this->_tmpFile);
            list($Timestamp, $NbAttempts) = explode(";", $fileContent);
            
            // We open the file
            $file = fopen($this->_tmpFile, "w");
            
            if(time() > $Timestamp+3600) {
                // If the last modification is more than an hour, the file is reset to 0
                fwrite($file, time().';1');
            }
            else {
                // Less than one hour
                
                if($NbAttempts > $this->_NbMaxAttemptsPerHour) {
                    $this->banSID();
                    $this->_error = 2;
                }
                elseif($NbAttempts > ($this->_NbMaxAttemptsPerHour-3)) {
                    // Warning message explaining that a maximum x number of connections/requests per hour is allowed
                    $this->_error = 1;
                }
            
                $NbAttempts++;
                
                // Update file
                fwrite($file, $Timestamp.';'.$NbAttempts);
            }
        }
        fclose($file);
    }
};
