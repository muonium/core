<?php
namespace application\controllers;
use \library\MVC as l;
use \application\models as m;

class User extends l\Languages {

    private $_modelFiles;
    private $_modelFolders;
    private $_modelStorage;

    private $_filename = ''; // current file uploaded

    private $_path = ''; // current path
    private $_folderId = 0; // current folder id (0 = root)

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

    function getFolderVars() {
        // User sent folder_id, initialize model folders, check if folder exists and set folder_id and path in class attributes
        $this->_modelFolders = new m\Folders();
        $this->_modelFolders->id_owner = $_SESSION['id'];

        if(empty($_POST['folder_id'])) {
            $this->_path = '';
            $this->_folderId = 0;
        }
        else if($_POST['folder_id'] === 0) {
            $this->_path = '';
            $this->_folderId = 0;
        }
        else {
            $folder_id = urldecode($_POST['folder_id']);
            if(!is_numeric($folder_id))
                return false;
            $path = $this->_modelFolders->getPath($folder_id);
            if($path === false)
                return false;
            $path .= $this->_modelFolders->getFoldername($folder_id);
            $this->_path = $path.'/';
            $this->_folderId = $folder_id;
        }
        return true;
    }

    function UpFilesAction() {
        $uploaded = 0;
        $this->getFolderVars();
        if(!empty($_FILES['upload'])) {
            $this->_modelFiles = new m\Files();
            $this->_modelFiles->id_owner = $_SESSION['id'];

            if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$this->_path)) {
                $this->_modelStorage = new m\Storage();
                $this->_modelStorage->id_user = $_SESSION['id'];
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
                        if(file_exists(NOVA.'/'.$_SESSION['id'].'/'.$this->_path.$_FILES['upload']['name'][$i]))
                            $exists = 1; // File already exists
                        if(move_uploaded_file($tmpFilePath, NOVA.'/'.$_SESSION['id'].'/'.$this->_path.$_FILES['upload']['name'][$i])) {
                            // File uploaded without errors
                            $this->_modelFiles->name = $_FILES['upload']['name'][$i];
                            $this->_modelFiles->size = $_FILES['upload']['size'][$i];
                            $this->_modelFiles->last_modification = time();
                            if($exists == 0) {
                                $this->_modelFiles->addNewFile($this->_folderId);
                                $stored += $_FILES['upload']['size'][$i];
                                $uploaded += $_FILES['upload']['size'][$i];
                            }
                            else {
                                $diff = $this->_modelFiles->updateFile($this->_folderId);
                                // updateFile returns the difference beetween the size of the new file and the size of the old file
                                $stored += $diff;
                                $uploaded += $diff;
                            }
                        }
                    }
                }

                $this->_modelStorage->updateSizeStored($stored);
                $this->_modelFolders->updateFoldersSize($this->_folderId, $uploaded);
            }
        }
    }

    function AddFolderAction() {
        $this->getFolderVars();
        if(!empty($_POST['folder'])) {
            $folder = urldecode($_POST['folder']);
            if(strlen($folder) > 64) // max length 64 chars
                $folder = substr($folder, 0, 64);

            $forbidden = '/\\:*?<>|" ';

            $f = 0;
            for($i=0;$i<count($forbidden);$i++) {
                if(strpos($folder, $forbidden[$i])) {
                    $f = 1; // Forbidden char found
                    break;
                }
            }

            if($f == 0) {
                if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$this->_path) && !is_dir(NOVA.'/'.$_SESSION['id'].'/'.$this->_path.$folder)) {
                    $this->_modelFolders->name = $folder;
                    $this->_modelFolders->parent = $this->_folderId;
                    $this->_modelFolders->path = $this->_path;
                    $this->_modelFolders->insert();
                    echo $this->_modelFolders->getLastInsertedId();
                    mkdir(NOVA.'/'.$_SESSION['id'].'/'.$this->_path.$folder, 0770);
                    return;
                }
            }
        }
        echo 'error';
    }

    function getTree() {
        $i = 0;
        $this->_modelFiles = new m\Files();
        $this->_modelFiles->id_owner = $_SESSION['id'];

        if(empty($this->_modelFolders)) {
            $this->_modelFolders = new m\Folders();
            $this->_modelFolders->id_owner = $_SESSION['id'];
        }

        $this->_modelStorage = new m\Storage();
        $this->_modelStorage->id_user = $_SESSION['id'];
        $quota = $this->_modelStorage->getUserQuota();
        $stored = $this->_modelStorage->getSizeStored();

        $time_start = microtime(true);

        // debug
        //echo '<p>folder_id : '.$this->_folderId.', path : '.$this->_path.'</p>';

        //echo '<p>['.$this->_path.']</p>';

        // Link to parent folder
        echo '<p>';
        if($this->_folderId != 0) {
            $parent = $this->_modelFolders->getParent($this->_folderId);
            echo '<a ondblclick="openDir('.$parent.')"><img src="'.IMG.'desktop/arrow.svg" class="icon"></a> ';
        }
        echo ' ['.$this->showSize($stored).'/'.$this->showSize($quota).']</p>';

        echo '<hr><div id="tree"> ';

        // New way
        if($subdirs = $this->_modelFolders->getChildren($this->_folderId)) {
            foreach($subdirs as $subdir)
                echo '<span class="folder" id="d'.$subdir['0'].'" name="'.htmlentities($subdir['1']).'" onclick="addFolderSelection(this.id)" ondblclick="openDir('.$subdir['0'].')"><img src="'.IMG.'desktop/extensions/folder.svg" class="icon"> <strong>'.htmlentities($subdir['1']).'</strong> ['.$this->showSize($subdir['2']).']</span>';
        }
        if($files = $this->_modelFiles->getFiles($this->_folderId)) {
            foreach($files as $file)
                echo '<span class="file" id="f'.$file['1'].'" onclick="addFileSelection(this.id)" title="'.htmlentities($file['0']).'">'.htmlentities($file['0']).' ['.$this->showSize($file['2']).'] - '.$this->txt->User->lastmod.' : '.date('d/m/Y G:i', $file['3']).'</span>';
        }

        $time_end = microtime(true);
        echo '</div><br />'.$this->txt->User->loaded.' '.($time_end-$time_start).' s';
    }

    function ChangePathAction() {
        if(!isset($_POST['folder_id']))
            $folder_id = 0;
        elseif(!is_numeric($_POST['folder_id']))
            return false;
        else
            $folder_id = urldecode($_POST['folder_id']);

        if($folder_id == 0) {
            // root
            $this->_path = '';
            $this->_folderId = 0;
            $this->getTree();
        }
        else {
            $this->_modelFolders = new m\Folders();
            $this->_modelFolders->id_owner = $_SESSION['id'];

            $path = $this->_modelFolders->getPath($folder_id);

            if($path === false)
                return false;
            $path .= $this->_modelFolders->getFoldername($folder_id);

            if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$path)) {
                $this->_path = $path;
                $this->_folderId = $folder_id;
                $this->getTree();
            }
        }
    }

    function rmFile($id) {
        if(!isset($this->_modelFiles)) {
            $this->_modelFiles = new m\Files();
            $this->_modelFiles->id_owner = $_SESSION['id'];
        }

        if(is_numeric($id)) {
            if($filename = $this->_modelFiles->getFilename($id)) {
                if(file_exists(NOVA.'/'.$_SESSION['id'].'/'.$this->_path.$filename)) {
                    unlink(NOVA.'/'.$_SESSION['id'].'/'.$this->_path.$filename);
                    // deleteFile() returns file size
                    return $this->_modelFiles->deleteFile($id);
                }
            }
        }
        return 0;
    }

    function RmFilesAction() {
        $this->getFolderVars();
        $this->_modelFiles = new m\Files();
        $this->_modelFiles->id_owner = $_SESSION['id'];

        $total_size = 0;
        if(!empty($_POST['files'])) {
            if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$this->_path)) {
                $files = explode("|", urldecode($_POST['files']));
                $nbFiles = count($files);
                if($nbFiles > 0) {
                    for($i=0;$i<$nbFiles;$i++)
                        $total_size += $this->rmFile($files[$i]);
                }
                // Decrement storage counter
                $this->_modelStorage = new m\Storage();
                $this->_modelStorage->id_user = $_SESSION['id'];
                $this->_modelStorage->decrementSizeStored($total_size);

                $this->_modelFolders->updateFoldersSize($this->_folderId, -1*$total_size);
            }
        }
        echo 'done';
    }

    function rmRdir($id) {
        // This is a recursive method
        if(is_numeric($id)) {
            $path = $this->_modelFolders->getPath($id);
            if($path !== false) {
                $foldername = $this->_modelFolders->getFoldername($id);
                if($foldername !== false) {
                    $full_path = NOVA.'/'.$_SESSION['id'].'/'.$path.$foldername;
                    if(is_dir($full_path)) {
                        // Delete subfolders
                        if($subdirs = $this->_modelFolders->getChildren($id)) {
                            foreach($subdirs as $subdir)
                                $this->rmRdir($subdir['0']);
                        }

                        // Delete files
                        foreach(glob("{$full_path}/*") as $file) {
                            if(is_file($file))
                                unlink($file);
                        }

                        // Delete files in db
                        $this->_modelFiles->deleteFiles($id);

                        // Delete folder
                        rmdir($full_path);
                    }
                }
            }
        }
    }

    function rmFolder($id) {
        if(!is_numeric($id))
            return 0;

        $size = $this->_modelFolders->getSize($id);
        if($size === false)
            return 0;

        // Delete folder, files, subfolders and also files in db
        $this->rmRdir($id);

        // Delete folders and subfolders in db and update parents folder size
        $this->_modelFolders->delete($id);
        return $size;
    }

    function RmFoldersAction() {
        $this->getFolderVars();
        $this->_modelFiles = new m\Files();
        $this->_modelFiles->id_owner = $_SESSION['id'];

        $total_size = 0;

        if(!empty($_POST['folders'])) {
            if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$this->_path)) {
                $folders = explode("|", urldecode($_POST['folders']));
                $nbFolders = count($folders);
                if($nbFolders > 0) {
                    for($i=0;$i<$nbFolders;$i++)
                        $total_size += $this->rmFolder($folders[$i]);
                }
                // Decrement storage counter
                $this->_modelStorage = new m\Storage();
                $this->_modelStorage->id_user = $_SESSION['id'];
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
            $this->_modelFiles = new m\Files();
            $this->_modelFiles->id_owner = $_SESSION['id'];
        }

        if(is_numeric($id)) {
            $file_name = $this->_modelFiles->getFullPath($id);
            if($file_name !== false) {
                if(file_exists($file_name)) {
                    $mime = 'application/octet-stream';
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
                    readfile($file_name);	// push it out
                }
            }
        }
    }

    // $src is the folder id of source folder
    // $dst is the folder id of dest folder where $src folder will be pasted
    function recurse_copy($src, $dst, $copy = '') {
        // This is a recursive method
        // Thank you "gimmicklessgpt at gmail dot com" from php.net for the base code
        // recurse_copy add also new files in db
        if($src == 0)
            return false;
        $foldername = $this->_modelFolders->getFoldername($src);
        if($foldername === false)
            return false;
        $size = $this->_modelFolders->getSize($src);
        if($size === false)
            return false;
        $src_path = $this->_modelFolders->getPath($src).$foldername.'/';

        if($dst == 0) {
            $dst_parent_path = '';
            $dst_parent_name = '';
        }
        else {
            $dst_parent_path = $this->_modelFolders->getPath($dst);
            $dst_parent_name = $this->_modelFolders->getFoldername($dst).'/';
        }

        if($copy == 'copy')
            $foldername .= ' (Copy)';
        //
        $this->_modelFolders->name = $foldername;
        $this->_modelFolders->parent = $dst;
        $this->_modelFolders->path = $dst_parent_path.$dst_parent_name;
        $this->_modelFolders->size = $size;
        $this->_modelFolders->insert();
        $folder_id = $this->_modelFolders->getLastInsertedId();
        //

        $dst_path = $this->_modelFolders->path.$foldername;

        @mkdir(NOVA.'/'.$_SESSION['id'].'/'.$dst_path, 0770);

        if($subdirs = $this->_modelFolders->getChildren($src)) {
            foreach($subdirs as $subdir)
                $this->recurse_copy($subdir['0'], $folder_id);
        }
        if($files = $this->_modelFiles->getFiles($src)) {
            foreach($files as $file) {
                copy(NOVA.'/'.$_SESSION['id'].'/'.$src_path.$file['0'], NOVA.'/'.$_SESSION['id'].'/'.$dst_path.'/'.$file['0']);
                // Add the new file in db
                $this->_modelFiles->name = $file['0'];
                $this->_modelFiles->last_modification = time();
                $this->_modelFiles->size = filesize(NOVA.'/'.$_SESSION['id'].'/'.$dst_path.'/'.$file['0']);
                $this->_modelFiles->addNewFile($folder_id);
            }
        }
    }

    function addSuffixe($file, $suffixe) {
        $double_extensions = array(
            'tar.gz',
            'tar.bz',
            'tar.xz',
            'tar.bz2'
        );

        $pos = strpos($file, '.');
        if($pos === false)
            return $file.$suffixe;

        $pathinfo = pathinfo($file);
        if(empty($pathinfo['extension']))
            return $file.$suffixe;

        $file_length = strlen($file);
        for($i=0;$i<count($double_extensions);$i++) {
            $length = strlen($double_extensions[$i])+1;
            if($file_length > $length) {
                $end = substr($file, -1*$length);
                if('.'.$double_extensions[$i] == $end) {
                    $start = substr($file, 0, $file_length-$length);
                    return $start.$suffixe.$end;
                }
            }
        }

        return $pathinfo['filename'].$suffixe.'.'.$pathinfo['extension'];
    }

    function MvAction() {
        $this->getFolderVars();
        // $copy : 0 => cut, 1 => copy

        if(!isset($_POST['copy']))
            $copy = 0;
        else {
            if($_POST['copy'] == 1)
                $copy = 1;
            else
                $copy = 0;
        }

        if(empty($_POST['files']) && empty($_POST['folders']))
            return;

        $this->_modelFiles = new m\Files();
        $this->_modelFiles->id_owner = $_SESSION['id'];
        $this->_modelFiles->folder_id = $this->_folderId;

        if(empty($_POST['old_folder_id'])) {
            $old_folder_id = 0;
            $old_path = '';
        }
        else if($_POST['old_folder_id'] === 0) {
            $old_folder_id = 0;
            $old_path = '';
        }
        else {
            $old_folder_id = urldecode($_POST['old_folder_id']);
            $old_path = $this->_modelFolders->getPath($old_folder_id);

            if($old_path === false)
                return false;
            $old_path .= $this->_modelFolders->getFoldername($old_folder_id).'/';
        }

        $this->_modelStorage = new m\Storage();
        $this->_modelStorage->id_user = $_SESSION['id'];
        $quota = $this->_modelStorage->getUserQuota();
        $stored = $this->_modelStorage->getSizeStored();
        $uploaded = 0;

        if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$this->_path) && is_dir(NOVA.'/'.$_SESSION['id'].'/'.$old_path)) {

            if(!empty($_POST['files'])) {
                $files = explode("|", urldecode($_POST['files']));
                if($copy == 0 && $this->_path != $old_path) {
                    //
                    // cut and paste files
                    //
                    for($i=0;$i<count($files);$i++) {
                        if(is_numeric($files[$i])) {
                            if(!($filename = $this->_modelFiles->getFilename($files[$i])))
                                continue;
                            if(file_exists(NOVA.'/'.$_SESSION['id'].'/'.$old_path.$filename)) {
                                rename(NOVA.'/'.$_SESSION['id'].'/'.$old_path.$filename, NOVA.'/'.$_SESSION['id'].'/'.$this->_path.$filename);
                                $this->_modelFiles->id = $files[$i];
                                $uploaded += filesize(NOVA.'/'.$_SESSION['id'].'/'.$this->_path.$filename);
                                $this->_modelFiles->updateDir();
                            }
                        }
                    }
                    // Update parent folders size
                    $this->_modelFolders->updateFoldersSize($old_folder_id, -1*$uploaded);
                }
                elseif($copy == 1) {
                    //
                    // copy and paste files
                    //
                    for($i=0;$i<count($files);$i++) {
                        if(is_numeric($files[$i])) {
                            if(!($filename = $this->_modelFiles->getFilename($files[$i])))
                                continue;
                            if(file_exists(NOVA.'/'.$_SESSION['id'].'/'.$old_path.$filename)) {
                                $this->_modelFiles->id = $files[$i];
                                $this->_modelFiles->size = filesize(NOVA.'/'.$_SESSION['id'].'/'.$old_path.$filename);
                                if($stored+$this->_modelFiles->size <= $quota) {
                                    $stored += $this->_modelFiles->size;
                                    $uploaded += $this->_modelFiles->size;
                                    $this->_modelFiles->last_modification = time();

                                    if($this->_path == $old_path) {
                                        $this->_modelFiles->name = $this->addSuffixe($filename, ' (Copy)');
                                        copy(NOVA.'/'.$_SESSION['id'].'/'.$old_path.$filename, NOVA.'/'.$_SESSION['id'].'/'.$this->_path.$this->_modelFiles->name);
                                    }
                                    else {
                                        $this->_modelFiles->name = $filename;
                                        copy(NOVA.'/'.$_SESSION['id'].'/'.$old_path.$filename, NOVA.'/'.$_SESSION['id'].'/'.$this->_path.$filename);
                                    }
                                    $this->_modelFiles->addNewFile($this->_folderId);
                                }
                            }
                        }
                    }
                }
            }

            if(!empty($_POST['folders'])) {
                $folders = explode("|", urldecode($_POST['folders']));
                if($copy == 0 && $this->_path != $old_path) {
                    //
                    // cut and paste folders
                    //
                    for($i=0;$i<count($folders);$i++) {
                        $foldername = $this->_modelFolders->getFolderName($folders[$i]);
                        if($foldername === false)
                            continue;
                        if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$old_path.$foldername)) {
                            $folderSize = $this->_modelFolders->getSize($folders[$i]);
                            $old_parent = $this->_modelFolders->getParent($folders[$i]);
                            rename(NOVA.'/'.$_SESSION['id'].'/'.$old_path.$foldername, NOVA.'/'.$_SESSION['id'].'/'.$this->_path.$foldername);
                            $this->_modelFolders->updatePath($folders[$i], $this->_path);
                            $this->_modelFolders->updateParent($folders[$i], $this->_folderId);

                            // Update parent folders size
                            $this->_modelFolders->updateFoldersSize($old_parent, -1*$folderSize);
                            $uploaded += $folderSize;
                        }
                    }
                }
                elseif($copy == 1) {
                    //
                    // copy and paste folders
                    //
                    for($i=0;$i<count($folders);$i++) {
                        $foldername = $this->_modelFolders->getFolderName($folders[$i]);
                        if($foldername === false)
                            continue;
                        if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$old_path.$foldername)) {
                            $folderSize = $this->_modelFolders->getSize($folders[$i]);
                            if($stored+$folderSize <= $quota) {
                                $stored += $folderSize;
                                $uploaded += $folderSize;
                                // recurse_copy add also new files and subfolders in db
                                if($this->_path == $old_path)
                                    $this->recurse_copy($folders[$i], $this->_folderId, 'copy');
                                else
                                    $this->recurse_copy($folders[$i], $this->_folderId);
                            }
                        }
                    }
                }
            }

            $this->_modelStorage->updateSizeStored($stored);
            if($uploaded != 0)
                $this->_modelFolders->updateFoldersSize($this->_folderId, $uploaded);
        }
    }
}
