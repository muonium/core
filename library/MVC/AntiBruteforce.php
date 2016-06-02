<?php
class AntiBruteforce
{
    private $_username;
    private $_ip;
    
    private $_tmpFile;
    private $_error = 0;
    
    // Folder to store temporary files
    private $_folder = 'tmp';
    
    private $_NbMaxAttemptsPerHour = 15; 
    
    function setUsername($username) {
        $this->_username = $username;
        // Temporary file with the number of connections/requests for the user, username is encrypted with sha1 and a salt for more security.
        $this->_tmpFile = $this->_folder.'/'.sha1($username.'c4$AZ_').'.tmp';
    }
    
    function setIP() {
        $this->_ip = $_SERVER['REMOTE_ADDR'];
        // Temporary file with the number of connections/requests for the ip, ip is encrypted with sha1 and a salt for more security.
        $this->_tmpFile = $this->_folder.'/'.sha1($this->_ip.'c4$AZ_').'.tmp';
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
    
    function banIP() {
        /*$ip = $_SERVER['REMOTE_ADDR'];
        $file = fopen("banned_ip.txt", "a");
        fwrite($file, $ip.';');
        fclose($file);*/
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
                    $this->banIP();
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
?>