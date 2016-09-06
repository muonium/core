<?php
class User extends Languages {

    /*private $_ArborescenceDossier = array();
    private $_SizeTotal;
    private $_SizeTotalOctet;
    private $_CheminUser;
    private $_Size;*/
    private $_modelFiles;
    
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
    
    function upFilesAction() {
        if(!empty($_FILES['upload'])) {
            $this->_modelFiles = new mFiles();
            $this->_modelFiles->setIdOwner($_SESSION['id']);
            if(!isset($_POST['path']))
                $path = '';
            else
                $path = $_POST['path'];

            if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$path)) {
                //echo $path.'<br />'.count($_FILES['upload']['name']);
                echo 'n:'.count($_FILES['upload']['name']).'<br />';
                for($i=0;$i<count($_FILES['upload']['name']);$i++) {
                    if(strlen($_FILES['upload']['name'][$i]) > 128) // max length 128 chars
                        $_FILES['upload']['name'][$i] = substr($_FILES['upload']['name'][$i], 0, 128);
                    //$this->_status = 'Uploading '.$_FILES['upload']['name'][$i];
                    $tmpFilePath = $_FILES['upload']['tmp_name'][$i];
                    if($tmpFilePath != "") {
                        // To do : Increment size_stored in storage table
                        // If size stored > user_quota => don't upload
                        echo 'Uploading '.$_FILES['upload']['name'][$i].'<br />';
                        $this->_modelFiles->setFile($_FILES['upload']['name'][$i]);
                        $this->_modelFiles->setSize($_FILES['upload']['size'][$i]);
                        $this->_modelFiles->setLastModification(time());
                        $this->_modelFiles->addNewFile($path);
                        move_uploaded_file($tmpFilePath, NOVA.'/'.$_SESSION['id'].'/'.$path.$_FILES['upload']['name'][$i]);
                    }
                }
                $upload_time = time() - $_SERVER['REQUEST_TIME'];
                echo 'Done. Upload time : '.$upload_time.'s';
            }
        }
    }
    
    function getUpFilesStatusAction() {
        // Progress bar shows total percentage, no file by file for now
        if(!empty($_SESSION["upload_progress_mui"])) {
            $current = $_SESSION["upload_progress_mui"]["bytes_processed"];
            $total = $_SESSION["upload_progress_mui"]["content_length"];
            $this->_filename = $_SESSION["upload_progress_mui"]["files"][0]["name"];
            echo $this->_filename.' : '.($current < $total ? ceil($current / $total * 100) : 100).'%';
        }
        else
            echo 'done';
    }
    
    function addFolderAction() {
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
                    mkdir(NOVA.'/'.$_SESSION['id'].'/'.$path.$folder, 0600);
            }
        }
        echo 'done';
    }
    
    function getTree() {
        $i = 0;
        $this->_modelFiles = new mFiles();
        $this->_modelFiles->setIdOwner($_SESSION['id']);
        
        $time_start = microtime(true);
        $files = $this->_modelFiles->getFiles($this->_path);
        
        echo '<p>['.$this->_path.']</p>';
        
        // Link to parent folder
        if($this->_path != '') {
            $lastPos = strrpos(substr($this->_path, 0, -1), "/");
            if($lastPos === false)
                echo '<p><a ondblclick="openDir(\'\')">ROOT</a></p>';
            else
                echo '<p><a ondblclick="openDir(\''.substr($this->_path, 0, $lastPos).'\')">^</a></p>';
        }
        
        echo '<hr>';
        
        if($handle = opendir(NOVA.'/'.$_SESSION['id'].'/'.$this->_path)) {
            while(false !== ($entry = readdir($handle))) {
                if($entry != '.' && $entry != '..') {
                    if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$this->_path.$entry)) {
                        echo '<span class="folder" id="d'.$i.'" name="'.$entry.'" onclick="addSelection(this.id)" ondblclick="openDirById(this.id)"><strong>'.$entry.'</strong></span>';
                        $i++;
                    }
                    else {
                        echo '<span class="file" id="f'.$files[$entry]['0'].'" onclick="addSelection(this.id)">'.$entry.' ['.$files[$entry]['1'].'o] - Last modification : '.date('d/m/Y G:i', $files[$entry]['2']).'</span>';
                    }
                }
            }
        }
        $time_end = microtime(true);
        echo '<br />Loaded in '.($time_end-$time_start).' s';
    }
    
    function changePathAction() {
        if(!isset($_POST['path']))
            $path = '';
        else
            $path = urldecode($_POST['path']);
        if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$path)) {
            $this->_path = $path;
            $this->getTree();
        }
    }
    
    function rmFilesAction() {
        $this->_modelFiles = new mFiles();
        $this->_modelFiles->setIdOwner($_SESSION['id']);
        
        $total_size = 0;
        
        if(!isset($_POST['path']))
            $path = '';
        else
            $path = urldecode($_POST['path']);
        if(!empty($_POST['files'])) {
            if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$path)) {
                $files = explode("|", $_POST['files']);
                $nbFiles = count($files);
                if($nbFiles > 1) {
                    for($i=0;$i<$nbFiles;$i++) {
                        if(is_numeric($files[$i])) {
                            if(file_exists(NOVA.'/'.$_SESSION['id'].'/'.$path.$files[$i])) {
                                if($filename = $this->_modelFiles->getFilename($files[$i])) {
                                    unlink(NOVA.'/'.$_SESSION['id'].'/'.$path.$filename);
                                    $total_size += $this->_modelFiles->deleteFile($files[$i]);
                                    // To do : decrement storage counter
                                }
                            }
                        }
                    }
                }
                //else
                    // rmFile()
            }
        }
        echo 'done';
    }
    
    function rmFoldersAction() {
        $this->_modelFiles = new mFiles();
        $this->_modelFiles->setIdOwner($_SESSION['id']);
        
        $total_size = 0;
        
        if(!isset($_POST['path']))
            $path = '';
        else
            $path = urldecode($_POST['path']);
        if(!empty($_POST['folders'])) {
            if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$path)) {
                $folders = explode("|", $_POST['folders']);
                $nbFolders = count($folders);
                if($nbFolders > 1) {
                    for($i=0;$i<$nbFolders;$i++) {
                        if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$path.$folders[$i])) {
                            rmdir(NOVA.'/'.$_SESSION['id'].'/'.$path.$folders[$i]);
                            // delete files in database
                            $total_size += $this->_modelFiles->deleteFiles($path.$folders[$i]);
                            // To do : decrement storage counter
                        }
                    }
                }
                //else
                    // rmFolder()
            }
        }
        echo 'done';
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

    function getSize() {
        return $this->_Size;
    }

    function setChemin($c) {
        $this->_CheminUser = $c;
    }

    function getArborescenceDossier() {
        $this->ArborescenceDossier($this->_CheminUser);
        return $this->_ArborescenceDossier;
    } 

    function AddDossier() {
        $chemin = '../nova/TestN1';
        if(!mkdir($chemin,0600,true)) {
            echo "Echec lors de la création du répertoire";
        }
        else 
        {
            echo "Création réussi";
        }
    }

    function ArborescenceDossier($chemin) {
        $lstat = lstat($chemin);
        $this->_SizeTotal += $lstat['size'];
        $folder = opendir ($chemin);

        while ($file = readdir($folder)) {
            if ($file != "." && $file != "..") {
                $pathfile = $chemin.'/'.$file;
                //if(filetype($pathfile) == 'dir'){
                $this->_ArborescenceDossier[] = $file;
                //$this->ArborescenceDossier($pathfile);
                /*} else if(filetype($pathfile) == 'file') {
						$this->_Arborescence[][] = $file;
					}
            }
        }
        closedir ($folder);  
    }

    function Arborescence($chemin) {
        $lstat = lstat($chemin);

        //echo $chemin ."   type : ".$filetype." - size : ".$lstat['size']." - mtime : ".$mtime.'<br/>';
        $this->_SizeTotal += $lstat['size'];
        $this->_SizeTotalOctet += $lstat['size']; 	
        if(is_dir($chemin)) {
            $me = opendir($chemin);
            while($child = readdir($me)) {
                //echo $child;
                if($child != '.' && $child != '..') {

                    $this->Arborescence($chemin.DIRECTORY_SEPARATOR.$child);
                }
            }
        }
    }

    function CalculTaille($nombre) {
        $Octet = 1;
        $KiloOctet = 1024 * $Octet;
        $MegaOctet = 1024 * $KiloOctet;
        $GigaOctet = 1024 * $MegaOctet;


        if($nombre  >= $KiloOctet ) {
            if($nombre  >= $MegaOctet) {
                if($nombre >= $GigaOctet) {
                    $nombre = $this->_SizeTotal / $GigaOctet;
                    $nombre = round($nombre);
                    $nombre = $nombre." Go";
                } else {
                    $nombre = $nombre / $MegaOctet;
                    $nombre = round($nombre);
                    $nombre = $nombre." Mo";
                }
            } else {
                $nombre = $nombre / $KiloOctet;
                $nombre = round($nombre);
                $nombre = $nombre." Ko";
            }
        } else {
            $nombre = $nombre." O";
        }
        return $nombre;
    }

    function getTailleTotal() {
        $Octet = 1;
        $KiloOctet = 1024 * $Octet;
        $MegaOctet = 1024 * $KiloOctet;
        $GigaOctet = 1024 * $MegaOctet;

        $this->Arborescence($this->_CheminUser);

        if($this->_SizeTotal  >= $KiloOctet ) {
            if($this->_SizeTotal  >= $MegaOctet) {
                if($this->_SizeTotal >= $GigaOctet) {
                    $this->_SizeTotal = $this->_SizeTotal / $GigaOctet;
                    $this->_SizeTotal = round($this->_SizeTotal,2);
                    $this->_SizeTotal = $this->_SizeTotal." Go";
                } else {
                    $this->_SizeTotal = $this->_SizeTotal / $MegaOctet;
                    $this->_SizeTotal = round($this->_SizeTotal,2);
                    $this->_SizeTotal = $this->_SizeTotal." Mo";
                }
            } else {
                $this->_SizeTotal = $this->_SizeTotal / $KiloOctet;
                $this->_SizeTotal = round($this->_SizeTotal,2);
                $this->_SizeTotal = $this->_SizeTotal." Ko";
            }
        } else {
            $this->_SizeTotal = $this->_SizeTotal." O";
        }
    }

    function getTaille() {

        $this->getTailleTotal();

        return $this->_SizeTotal;
    }*/
}
?>