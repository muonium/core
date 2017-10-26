<?php
namespace library\MVC;

class AntiBruteforce {
    private $_id;
    private $_sid;
    private $_tmpFile;
    private $_error = 0;

    // Folder to store temporary files
    private $_folder = 'tmp';

    private $_prefix = '';
    private $_nbMaxAttemptsPerHour = 15;

    function setId($id, $prefix = '') {
        $this->_prefix = $prefix;
        $this->_id = $id;
        // Temporary file with the number of connections/requests for the user, username is encrypted with sha1 and a salt for more security.
        $this->_tmpFile = $this->_folder.'/'.$prefix.sha1($id.'c4$AZ_').'.tmp';
    }

    function setSID($prefix = '') {
        $this->_prefix = $prefix;
        $this->_sid = session_id();
        // Temporary file with the number of connections/requests for the session id, session id is encrypted with sha1 and a salt for more security.
        $this->_tmpFile = $this->_folder.'/'.$prefix.sha1(session_id().'c4$AZ_').'.tmp';
    }

    function setFolder($folder) {
        $this->_folder = $folder;
    }

    function setNbMaxAttemptsPerHour($nbMaxAttemptsPerHour) {
        $this->_nbMaxAttemptsPerHour = $nbMaxAttemptsPerHour;
    }

    function getError() {
        return $this->_error;
    }

    function getNbMaxAttemptsPerHour() {
        return $this->_nbMaxAttemptsPerHour;
    }

    function banSID() {
        unlink($this->_folder.'/'.$this->_prefix.sha1(session_id().'c4$AZ_').'.tmp');
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
            list($timestamp, $nbAttempts) = explode(";", $fileContent);

            // We open the file
            $file = fopen($this->_tmpFile, "w");

            if(time() > $timestamp+3600) {
                // If the last modification is more than an hour, the file is reset to 0
                fwrite($file, time().';1');
            }
            else {
                // Less than one hour
                if($nbAttempts > $this->_nbMaxAttemptsPerHour) {
                    $this->banSID();
                    $this->_error = 2;
                }
                elseif($nbAttempts > ($this->_nbMaxAttemptsPerHour-3)) {
                    // Warning message explaining that a maximum x number of connections/requests per hour is allowed
                    $this->_error = 1;
                }
                $nbAttempts++;

                // Update file
                fwrite($file, $timestamp.';'.$nbAttempts);
            }
        }
        fclose($file);
    }
}
