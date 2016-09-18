<?php
class User extends Languages {

    private $_modelFiles;
    private $_modelStorage;

    private $_filename = ''; // current file uploaded

    private $_path = ''; // current path

    function __construct() {
        parent::__construct();
        if(empty($_SESSION['id']))
            exit(header('Location: '.MVC_ROOT.'/Error/Error/404'));
        if(!empty($_SESSION['validate']))
            exit(header('Location: '.MVC_ROOT.'/Validate'));
    }

    function DefaultAction() {
        include(DIR_VIEW."vUser.php");
    }

    function UpFilesAction() {
        if(!empty($_FILES['upload'])) {
            $this->_modelFiles = new mFiles();
            $this->_modelFiles->setIdOwner($_SESSION['id']);
            if(!isset($_POST['path']))
                $path = '';
            else
                $path = urldecode($_POST['path']);

            if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$path)) {
                $this->_modelStorage = new mStorage();
                $this->_modelStorage->setIdUser($_SESSION['id']);

                $quota = $this->_modelStorage->getUserQuota();
                if($quota === false)
                    return false;
                $stored = $this->_modelStorage->getSizeStored();
                if($stored === false)
                    return false;

                for($i=0;$i<count($_FILES['upload']['name']);$i++) {
                    $_FILES['upload']['name'][$i] = str_replace("|", "", $_FILES['upload']['name'][$i]); // | is not allowed
                    if(strlen($_FILES['upload']['name'][$i]) > 128) // max length 128 chars
                        $_FILES['upload']['name'][$i] = substr($_FILES['upload']['name'][$i], 0, 128);
                    $tmpFilePath = $_FILES['upload']['tmp_name'][$i];
                    if($tmpFilePath != "") {
                        // If size stored > user_quota => don't upload
                        if(($stored+$_FILES['upload']['size'][$i]) > $quota)
                            break;

                        $exists = 0;
                        if(file_exists(NOVA.'/'.$_SESSION['id'].'/'.$path.$_FILES['upload']['name'][$i]))
                            $exists = 1; // File already exists
                        
                        if(move_uploaded_file($tmpFilePath, NOVA.'/'.$_SESSION['id'].'/'.$path.$_FILES['upload']['name'][$i])) {
                            // File uploaded without errors

                            $this->_modelFiles->setFile($_FILES['upload']['name'][$i]);
                            $this->_modelFiles->setSize($_FILES['upload']['size'][$i]);
                            $this->_modelFiles->setLastModification(time());
                            if($exists == 0) {
                                $this->_modelFiles->addNewFile($path);
                                $stored += $_FILES['upload']['size'][$i];
                            }
                            else {
                                $stored += $this->_modelFiles->updateFile($path);
                                // updateFile returns the difference beetween the size of the new file and the size of the old file
                            }
                        }
                    }
                }

                $this->_modelStorage->updateSizeStored($stored);
            }
        }
    }

    function AddFolderAction() {
        if(!empty($_POST['folder'])) {
            $folder = urldecode($_POST['folder']);
            if(strlen($folder) > 64) // max length 64 chars
                $folder = substr($folder, 0, 64);

            if(!isset($_POST['path']))
                $path = '';
            else
                $path = urldecode($_POST['path']);
            $forbidden = '/\\:*?<>|" ';

            $f = 0;
            for($i=0;$i<count($forbidden);$i++) {
                if(strpos($folder, $forbidden[$i])) {
                    $f = 1; // Forbidden char found
                    break;
                }
            }

            //echo 'debug:'.$folder.':'.$path.':'.$f.'<br />';

            if($f == 0) {
                if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$path) && !is_dir(NOVA.'/'.$_SESSION['id'].'/'.$path.$folder))
                    mkdir(NOVA.'/'.$_SESSION['id'].'/'.$path.$folder, 0770);
            }
        }
        echo 'done';
    }

    function getTree() {
        $i = 0;
        $this->_modelFiles = new mFiles();
        $this->_modelFiles->setIdOwner($_SESSION['id']);

        $this->_modelStorage = new mStorage();
        $this->_modelStorage->setIdUser($_SESSION['id']);
        $quota = $this->_modelStorage->getUserQuota();
        $stored = $this->_modelStorage->getSizeStored();
        $time_start = microtime(true);
        $files = $this->_modelFiles->getFiles($this->_path);

        echo '<p>['.$this->_path.']</p>';

        // Link to parent folder
        echo '<p>';
        if($this->_path != '') {
            $lastPos = strrpos(substr($this->_path, 0, -1), "/");
            if($lastPos === false)
                echo '<a ondblclick="openDir(\'\')">ROOT</a> ';
            else
                echo '<a ondblclick="openDir(\''.substr($this->_path, 0, $lastPos).'\')">^</a> ';
        }
        echo ' ['.$this->showSize($stored).'/'.$this->showSize($quota).']</p>';

        echo '<hr>';

        if($handle = opendir(NOVA.'/'.$_SESSION['id'].'/'.$this->_path)) {
            while(false !== ($entry = readdir($handle))) {
                if($entry != '.' && $entry != '..') {
                    if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$this->_path.$entry)) {
                        echo '<span class="folder" id="d'.$i.'" name="'.$entry.'" onclick="addSelection(this.id)" ondblclick="openDirById(this.id)"><strong>'.$entry.'</strong></span>';
                        $i++;
                    }
                    else {
                        echo '<span class="file" id="f'.$files[$entry]['0'].'" onclick="addSelection(this.id)">'.$entry.' ['.$this->showSize($files[$entry]['1']).'] - Last modification : '.date('d/m/Y G:i', $files[$entry]['2']).'</span>';
                    }
                }
            }
        }
        $time_end = microtime(true);
        echo '<br />Loaded in '.($time_end-$time_start).' s';
    }

    function ChangePathAction() {
        if(!isset($_POST['path']))
            $path = '';
        else
            $path = urldecode($_POST['path']);
        if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$path)) {
            $this->_path = $path;
            $this->getTree();
        }
    }

    function rmFile($path, $id) {
        if(!isset($this->_modelFiles)) {
            $this->_modelFiles = new mFiles();
            $this->_modelFiles->setIdOwner($_SESSION['id']);
        }
        
        if(is_numeric($id)) {
            if($filename = $this->_modelFiles->getFilename($id)) {
                if(file_exists(NOVA.'/'.$_SESSION['id'].'/'.$path.$filename)) {
                    unlink(NOVA.'/'.$_SESSION['id'].'/'.$path.$filename);
                    // deleteFile() returns file size
                    return $this->_modelFiles->deleteFile($id);
                }
            }
        }
        return 0;
    }

    function RmFilesAction() {
        $this->_modelFiles = new mFiles();
        $this->_modelFiles->setIdOwner($_SESSION['id']);

        $total_size = 0;
        if(!isset($_POST['path']))
            $path = '';
        else
            $path = urldecode($_POST['path']);
        if(!empty($_POST['files'])) {
            if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$path)) {
                $files = explode("|", urldecode($_POST['files']));
                $nbFiles = count($files);
                if($nbFiles > 0) {
                    for($i=0;$i<$nbFiles;$i++)
                        $total_size += $this->rmFile($path, $files[$i]);
                }
                // Decrement storage counter
                $this->_modelStorage = new mStorage();
                $this->_modelStorage->setIdUser($_SESSION['id']);
                $this->_modelStorage->decrementSizeStored($total_size);
            }
        }
        echo 'done';
    }

    function rmRdir($path) {
        // This function is like rmdir() but it works when there are files and folders inside.
        //In fact : "R" for "recursive" like "rm -r" on Unix* like
        foreach(glob("{$path}/*") as $file)
        {
            if(is_dir($file))
                $this->rmRdir($file);
            else
                unlink($file);
        }
        rmdir($path);
    }

    function rmFolder($path, $name) {
        if(!isset($this->_modelFiles)) {
            $this->_modelFiles = new mFiles();
            $this->_modelFiles->setIdOwner($_SESSION['id']);
        }
        
        if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$path.$name)) {
            $this->rmRdir(NOVA.'/'.$_SESSION['id'].'/'.$path.$name);
            // delete files in database
            // deleteFiles() returns total file size
            return $this->_modelFiles->deleteFiles($path.$name);
        }
        return 0;
    }

    function RmFoldersAction() {
        $this->_modelFiles = new mFiles();
        $this->_modelFiles->setIdOwner($_SESSION['id']);

        $total_size = 0;

        if(!isset($_POST['path']))
            $path = '';
        else
            $path = urldecode($_POST['path']);
        if(!empty($_POST['folders'])) {
            if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$path)) {
                $folders = explode("|", urldecode($_POST['folders']));
                $nbFolders = count($folders);
                if($nbFolders > 0) {
                    for($i=0;$i<$nbFolders;$i++)
                        $total_size += $this->rmFolder($path, $folders[$i]);
                }
                // Decrement storage counter
                $this->_modelStorage = new mStorage();
                $this->_modelStorage->setIdUser($_SESSION['id']);
                $this->_modelStorage->decrementSizeStored($total_size);
            }
        }
        echo 'done';
    }

    function showSize($size, $precision = 2) {
        // $size => size in bytes
        if(!is_numeric($size))
            return 0;
        if($size <= 0)
            return 0;
        $base = log($size, 1024);
        $suffixes = array('', 'K', 'M', 'G', 'T');

        return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
    }
    
    function DownloadAction($id) {
        if(!isset($this->_modelFiles)) {
            $this->_modelFiles = new mFiles();
            $this->_modelFiles->setIdOwner($_SESSION['id']);
        }
        
        if(is_numeric($id)) {
            if($filename = $this->_modelFiles->getFilename($id)) {
                if(file_exists(NOVA.'/'.$_SESSION['id'].'/'.$path.$filename)) { 
                    $file_name = NOVA.'/'.$_SESSION['id'].'/'.$path.$filename;
                    $mime = 'application/force-download';
                    header('Pragma: public'); 	// required
                    header('Expires: 0');		// no cache
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($file_name)).' GMT');
                    header('Cache-Control: private',false);
                    header('Content-Type: '.$mime);
                    header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
                    header('Content-Transfer-Encoding: binary');
                    header('Content-Length: '.filesize($file_name));	// provide file size
                    header('Connection: close');
                    readfile($file_name);		// push it out*/
                }
            }
        }
    }

    //
    // Functions below could be modified
    //

    /*function getLastModification($chemin) {
        $lstat = lstat($chemin);
        $mtime = date('d/m/Y H:i', $lstat['mtime']);
        return $mtime;

    }

    function getTailleDossier($chemin) {
        $this->_Size = 0;
        //$lstat = lstat($chemin);
        //$this->_Size += $lstat['size'];
        //echo $this->_Size;
        $pDossier = opendir($chemin);
        while($file = readdir($pDossier)){
            if($file != '.' && $file != '..') {
                $pathfile = $chemin.'/'.$file;
                $lstat = lstat($pathfile);
                //echo $lstat['size'];
                $this->_Size += $lstat['size'];
            }
        }
        closedir($pDossier);

    }
    */
}
?>
