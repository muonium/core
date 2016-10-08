/*var Miu =
{
    // Initialisation de l'interface
    //
    init: function()
    {
        // Chargement du contenu de l'onglet "Général"
    }
};*/

/* global.js : User's interface */

// Vars

var Box;
var Area = 0; // 0 : desktop div, 1 : file, 2 : folder
var addSel = 0; // 1 => add a new selection

var MoveFile = [];     // Contains file(s) id cut/copied
var MoveFolder = [];   // Contains folder(s) name cut/copied

var Copy = 0; // 0 : cut, 1 : copy
var mvPath = ''; // path where files/folders to move are located

var SelectedFile = [];      // Selected files
var SelectedFolder = [];    // Selected folders

var path = ''; // Current path

var filesUploaded = 0; // Number of files uploaded

var returnArea;

// Box class. Show a div 'box' when user uses right click inside desktop div, close the box when user uses left click

var box = function() {
    this.box_div = document.querySelector("#box");
    this.x = 0;
    this.y = 0;
}

box.prototype.left_click = function(x, y) {
    // If the user uses left click inside the 'box'
    if((x > this.x && x < this.x + this.box_div.clientWidth) && (y > this.y && y < this.y + this.box_div.clientHeight)) {
        // Action
    }
    else {
        // Otherwise, hide 'box'
        this.box_div.style.display = 'none';
        Area = 0;
    }
}

box.prototype.right_click = function(x, y, id) {
    // Show box at position x, y
    this.x = x;
    this.y = y;

    this.box_div.style.left = x+'px';
    this.box_div.style.top = y+'px';

    if(id === undefined) {
        //when there isn't anything
        //under the mouse
        Area = 0;
    }

    // Content according to area
    switch(Area) {
        //over nothing
        case 0:
            this.box_div.innerHTML = '<p onclick="nFolder()"><img src="'+img+'desktop/actions/create_folder.svg" class="icon"> '+txt.RightClick.nFolder+'</p><p onclick="upFilesDialog()"><img src="'+img+'desktop/actions/upload.svg" class="icon"> '+txt.RightClick.upFiles+'</p>';
            if(MoveFile.length > 0 || MoveFolder.length > 0) { this.box_div.innerHTML += '<hr><p onclick="paste(\''+id+'\')"><img src="'+img+'index/actions/paste.svg" class="icon"> '+txt.RightClick.paste+'</p>'; }
            this.box_div.innerHTML += '<hr><p onclick="logout()">'+txt.RightClick.logOut+'</p>';
            break;
        //mouse over a file
        case 1:
            this.box_div.innerHTML = '<p onclick="dl(\''+id+'\')"><img src="'+img+'index/actions/download.svg" class="icon"> '+txt.RightClick.dl+'</p><hr><p><img src="'+img+'index/actions/putInFavorites.svg" class="icon"> '+txt.RightClick.star+'</p><hr><p onclick="cut(\''+id+'\')"><img src="'+img+'index/actions/cut.svg" class="icon"> '+txt.RightClick.cut+'</p><p onclick="copy(\''+id+'\')"><img src="'+img+'index/actions/copy.svg" class="icon"> '+txt.RightClick.copy+'</p><p onclick="paste(\''+id+'\')"><img src="'+img+'index/actions/paste.svg" class="icon"> '+txt.RightClick.paste+'</p><p onclick="rm(\''+id+'\')">'+txt.RightClick.rm+'</p><hr><p><img src="'+img+'index/actions/rename.svg" class="icon"> '+txt.RightClick.mvItem+'</p><p><img src="'+img+'index/actions/paste.svg" class="icon">'+txt.RightClick.mvLocate+'</p><hr><p>'+txt.RightClick.vDetails+'</p>';
            break;
        //mouse over a folder
        case 2:
            this.box_div.innerHTML = '<p onclick="openDirById(\''+id+'\')"><img src="'+img+'index/actions/view.svg" class="icon"> '+txt.RightClick.open+'</p><hr><p onclick="cut(\''+id+'\')"><img src="'+img+'index/actions/cut.svg" class="icon"> '+txt.RightClick.cut+'</p><p onclick="copy(\''+id+'\')"><img src="'+img+'index/actions/copy.svg" class="icon"> '+txt.RightClick.copy+'</p><p onclick="paste(\''+id+'\')"><img src="'+img+'index/actions/paste.svg" class="icon"> '+txt.RightClick.paste+'</p><p onclick="rm(\''+id+'\')">'+txt.RightClick.rm+'</p><hr><p><img src="'+img+'index/actions/rename.svg" class="icon"> '+txt.RightClick.mvItem+'</p><p>'+txt.RightClick.mvLocate+'</p><hr><p>'+txt.RightClick.vDetails+'</p>';
    }
    this.box_div.style.display = 'block';
}

window.oncontextmenu = function(event) {
    // Disable right click
    return false;
}

window.onclick = function(event) {
    // Left click
    Box.left_click(event.clientX, event.clientY);
    // reset selected folders/files
    if(addSel == 0) {
        for(var i=0;i<SelectedFile.length;i++)
            document.querySelector("#f"+SelectedFile[i]).style.backgroundColor="white";
        for(var i=0;i<SelectedFolder.length;i++)
            if(folderId = getFolderId(SelectedFolder[i]))
                document.querySelector("#"+folderId).style.backgroundColor="white";
        SelectedFile = [];
        SelectedFolder = [];
    }
    else
        addSel = 0;
}

window.onload = function() {

    // Get txt from user's language json (language.js)
    getJSON();

    /*
    */

    returnArea = document.querySelector("#returnArea");

    window.addEventListener("keydown", function(event) {
        if(event.ctrlKey && event.keyCode == 68) {
            event.preventDefault(); // disable the hotkey in web browser
            logout();
        }
        else if(event.ctrlKey && event.keyCode == 65) {
            event.preventDefault(); // disable the hotkey in web browser
            selectAll();
        }
        else if(event.ctrlKey && event.keyCode == 73) {
            event.preventDefault(); // disable the hotkey in web browser
            invertSelection();
        }
        switch(event.keyCode) {
            case 46:
                // suppr
                rmMultiple();
                break;
            case 27:
                // esc
                document.querySelector("#box").style.display = 'none';
                break;
        }
    });
    Box = new box();

    // Right click inside desktop div
    document.querySelector("#desktop").addEventListener("contextmenu", function(event) {
        //event.preventDefault();
        if(Area == 0) {
            // If we are inside desktop but not inside its children
            Box.right_click(event.clientX, event.clientY);
        }
        else {
            // If we are inside its children, set Area to 0 because this function is always called when user call file's actions or folder's actions
            // Next, we will be able to use right click inside desktop div (area = 0) and when we
            // call file's actions or folder's actions, 'box' for 'desktop' area will not be displayed
            Area = 0;
        }
        return false;
    });

    // Set events for all files and folders loaded
    setEvents();
}

var setEvents = function() {
    // Right click inside divs with file's class (these divs are children of 'desktop')
    // After the execution of the function below, the function for 'desktop' above will be
    //called automatically (because we are inside desktop) and will set Area to 0 without displaying a new 'box'
    // Files actions
    var files = document.querySelectorAll(".file");
    for (var i = 0; i < files.length; i++) {
        // For each file
        files[i].addEventListener("contextmenu", function(event) {
            // Right click
            Area = 1;
            // Call right_click function with div's id
            Box.right_click(event.clientX, event.clientY, this.id);
            return false;
        });
    }

    // Right click inside divs with folder's class (these divs are children of 'desktop')
    // After the execution of the function below, the function for 'desktop'
    //above will be called automatically (because we are inside desktop) and will set Area to 0 without displaying a new 'box'
    // Folders actions
    var folders = document.querySelectorAll(".folder");
    for (var i = 0; i < folders.length; i++) {
        // For each folder
        folders[i].addEventListener("contextmenu", function(event) {
            // Right click
            Area = 2;
            // Call right_click function with div's id
            Box.right_click(event.clientX, event.clientY, this.id);
            return false;
        });
    }
}

var isNumeric = function(n) {
    if(typeof(n) == "string")
        n = n.replace(",", ".");
    return !isNaN(parseFloat(n)) && isFinite(n);
}

var cleanPath = function(p) {
    // format : dir1/dir2/
    if(p == '/')
        return '';
    if(p.length > 1) {
        if(p.substr(0, 1) == '/')
            p = p.substr(1);
        var p0 = p.split("/");
        for(var i=0;i<p0.length;i++)
            if(p0[i] == '')
                p0.splice(i, 1);
        p = p0.join('/');
        if(p.substr(-1) != '/')
            p = p+'/';
    }
    return p;
}

var logout = function() {
    window.location.href="Logout";
    return false;
}

var nFolder = function() {
    if(document.querySelector("#nFolder")) {
        document.querySelector("#box").style.display="none";

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "User/AddFolder", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function()
        {
            if(xhr.status == 200 && xhr.readyState == 4)
            {
                console.log(xhr.response);
                window.location.href="User";
                // To do : add folder to the div without reloading
            }
        }
        xhr.send("path="+encodeURIComponent(path)+"&folder="+encodeURIComponent(document.querySelector("#nFolder").value));

        // Refresh

        // Hide box
        //document.querySelector("#box").style.display = 'none';
    }
    else {
        document.querySelector("#box").innerHTML = txt.User.folder+' : <input type="text" id="nFolder" onkeypress="return verifFolderName(event);">';
    }
}

var verifFolderName = function(evt) {
    var keyCode = evt.which ? evt.which : evt.keyCode;
    if(keyCode == 13) { // Submit
        nFolder();
        return false;
    }
    var interdit = '/\\:*?<>|"';
    if (interdit.indexOf(String.fromCharCode(keyCode)) >= 0)
        return false;
    return true;
}

var upFilesDialog = function() {
    document.querySelector('#upFilesInput').click();
}

//
var xhr_upload = new Array();

var abort = function(i) {
    document.querySelector("#div_upload"+i).style.display = 'none';
    xhr_upload[i].abort();
    console.log("aborted "+i);
    filesUploaded++;
}

var upFiles = function(files) {
    // Upload multiple files function
    xhr_upload = new Array();
    var progress = document.querySelector("#progress");
    progress.innerHTML = ' ';
    // Loop through each of the selected files.
    for(var i=0;i<files.length;i++) {
        progress.innerHTML += '<div id="div_upload'+i+'"><button onclick="abort('+i+')">X</button> <span id="span_upload'+i+'"></span></div>';
        upFile(files[i], i);
    }

    // Waiting end of the uploading process
    var timer = setInterval(function() {
        console.log("waiting...");
        if(filesUploaded >= files.length) {
            progress.innerHTML = ' ';
            clearInterval(timer);
            openDir(path);
        }
    }, 1000);
}

var upFile = function(file, i) {
    // Upload a file

    // Create a new FormData object.
    var formData = new FormData();

    // Add the file to the request.
    formData.append('path', path);
    formData.append('upload[]', file, file.name);
    xhr_upload[i] = new XMLHttpRequest();
    xhr_upload[i].open("POST", "User/UpFiles", true);

    // Progress bar
    xhr_upload[i].upload.addEventListener("progress", function(event, filename) {
        if(event.lengthComputable)
            document.querySelector("#span_upload"+i).innerHTML = file.name+" : "+(event.loaded/event.total*100).toFixed(2)+"%";
    }, false);

    xhr_upload[i].onreadystatechange = function() {
        if(xhr_upload[i].readyState === 4) {
            if(xhr_upload[i].status === 200) {
                console.log(xhr_upload[i].responseText);
                filesUploaded++;
            }
        }
    };
    xhr_upload[i].send(formData);
}
//

var getFolderName = function(id) {
    if(document.getElementById(id))
        return document.getElementById(id).getAttribute("name");
    return false;
}

var getFolderId = function(name) {
    if(document.getElementsByName(name))
        return document.getElementsByName(name)[0].getAttribute("id");
    return false;
}

var openDirById = function(dir) {
    var id = 0;
    if(dir.length > 1) {
        id = dir.substr(1);
        if(isNumeric(id) && dir.substr(0, 1) == 'd')
            openDir(path+'/'+getFolderName(dir));
    }
}

var openDir = function(dir) {
    var dirName = cleanPath(dir);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "User/ChangePath", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function()
    {
        if(xhr.status == 200 && xhr.readyState == 4)
        {
            if(xhr.responseText != '') {
                // Hide box
                document.querySelector("#box").style.display = 'none';

                document.querySelector("#tree").innerHTML = xhr.responseText;
                path = dirName;
                // reset selected files/folders
                SelectedFile = [];
                SelectedFolder = [];
                // Set events for all files and folders loaded
                setEvents();

                // Put icons
                var ext = '';
                var pos = -1;
                var icon;

                // Types of files
                var archive = ['zip', 'tar', 'gz', 'bz', 'bz2', 'xz', 'rar', 'jar', '7z'];
                var code = ['php', 'html', 'htm', 'php3', 'php4', 'php5', 'java', 'css', 'scss', 'xml', 'svg', 'sql', 'c', 'cpp', 'cs', 'js', 'au3', 'asm', 'h', 'ini', 'jav', 'p', 'pl', 'rb', 'sh', 'bat', 'py'];
                var image = ['jpg', 'jpeg', 'png', 'bmp', 'gif'];
                var doc = ['docx', 'odt', 'doc'];
                var pdf = ['pdf'];
                var sound = ['mp3', 'ogg', 'flac', 'wav', 'aac'];
                var video = ['mp4', 'avi', 'wmv', 'mpeg', 'mov', 'mkv', 'mka', 'mks', 'flv'];

                var dir_files = document.querySelectorAll(".file");
                for(var i = 0; i < dir_files.length; i++) {
                    icon = 'text';
                    pos = dir_files[i].title.lastIndexOf('.');
                    if(pos !== -1) {
                        ext = dir_files[i].title.substr(pos+1);
                        if(archive.indexOf(ext) !== -1)
                            icon = 'archive';
                        else if(code.indexOf(ext) !== -1)
                            icon = 'code';
                        else if(image.indexOf(ext) !== -1)
                            icon = 'image';
                        else if(doc.indexOf(ext) !== -1)
                            icon = 'doc';
                        else if(pdf.indexOf(ext) !== -1)
                            icon = 'pdf';
                        else if(sound.indexOf(ext) !== -1)
                            icon = 'sound';
                        else if(video.indexOf(ext) !== -1)
                            icon = 'video';
                    }
                    dir_files[i].innerHTML = '<img src="'+img+'desktop/extensions/'+icon+'.svg" class="icon"> '+dir_files[i].innerHTML;
                }
            }
        }
    }
    xhr.send("path="+encodeURIComponent(dirName));
}

var addFileSelection = function(id) {
    addSel = 1;
    if(document.querySelector("#"+id)) {
        var pos = SelectedFile.indexOf(id.substr(1));
        if(pos != -1) {
            SelectedFile.splice(pos, 1);
            document.querySelector("#"+id).style.backgroundColor='white';
        }
        else {
            SelectedFile.push(id.substr(1));
            document.querySelector("#"+id).style.backgroundColor='#E0F0FA';
        }
    }
}

var addFolderSelection = function(id) {
    addSel = 1;
    if(document.querySelector("#"+id)) {
        var pos = SelectedFolder.indexOf(getFolderName(id));
        if(pos != -1) {
            SelectedFolder.splice(pos, 1);
            document.querySelector("#"+id).style.backgroundColor='white';
        }
        else {
            SelectedFolder.push(getFolderName(id));
            document.querySelector("#"+id).style.backgroundColor='#E0F0FA';
        }
    }
}

var invertSelection = function() {
    var i = 0;
    var files = document.querySelectorAll(".file");
    for(i=0;i<files.length;i++)
        addFileSelection(files[i].id);

    var folders = document.querySelectorAll(".folder");
    for(i=0;i<folders.length;i++)
        addFolderSelection(folders[i].id);
}

var selectAll = function() {
    addSel = 1;
    var i = 0;
    var files = document.querySelectorAll(".file");
    for(i=0;i<files.length;i++) {
        if(document.querySelector("#"+files[i].id)) {
            if(SelectedFile.indexOf((files[i].id).substr(1)) == -1) {
                SelectedFile.push((files[i].id).substr(1));
                document.querySelector("#"+files[i].id).style.backgroundColor='#E0F0FA';
            }
        }
    }

    var folders = document.querySelectorAll(".folder");
    for(i=0;i<folders.length;i++) {
        if(document.querySelector("#"+folders[i].id)) {
            if(SelectedFolder.indexOf(getFolderName(folders[i].id)) == -1) {
                SelectedFolder.push(getFolderName(folders[i].id));
                document.querySelector("#"+folders[i].id).style.backgroundColor='#E0F0FA';
            }
        }
    }
}

var cut = function(id) {
    document.querySelector("#box").style.display="none";
    Copy = 0;

    // reset
    MoveFile = [];
    MoveFolder = [];

    if(SelectedFile.length == 0 && SelectedFolder.length == 0) {
        // cut only the file/folder selected
        if(id.length > 0) {
            if(id.substr(0, 1) == 'd') {
                if(folderName = getFolderName(id))
                    MoveFolder.push(folderName);
            }
            if(id.substr(0, 1) == 'f')
                MoveFile.push(id.substr(1));
        }
    }
    else {
        // cut all selected files/folders
        MoveFile = SelectedFile;
        MoveFolder = SelectedFolder;
    }
    mvPath = path;
}

var copy = function(id) {
    document.querySelector("#box").style.display="none";
    Copy = 1;

    // reset
    MoveFile = [];
    MoveFolder = [];

    if(SelectedFile.length == 0 && SelectedFolder.length == 0) {
        // cut only the file/folder selected
        if(id.length > 0) {
            if(id.substr(0, 1) == 'd') {
                if(folderName = getFolderName(id))
                    MoveFolder.push(folderName);
            }
            if(id.substr(0, 1) == 'f')
                MoveFile.push(id.substr(1));
        }
    }
    else {
        // cut all selected files/folders
        MoveFile = SelectedFile;
        MoveFolder = SelectedFolder;
    }
    mvPath = path;
}

var paste = function() {
    document.querySelector("#box").style.display="none";
    var id = 0;
    var folderName;

    var folders = [];
    var files = [];

    if(MoveFile.length > 0 || MoveFolder.length > 0) {
        for(var i=0; i<MoveFile.length; i++) {
            if(MoveFile[i].length > 0 && isNumeric(MoveFile[i]))
                files.push(MoveFile[i]);
        }

        for(var i=0; i<MoveFolder.length; i++) {
            if(MoveFolder[i].length > 0)
                folders.push(MoveFolder[i]);
        }

        folders = encodeURIComponent(folders.join('|'));
        files = encodeURIComponent(files.join('|'));

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "User/Mv", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function()
        {
            if(xhr.status == 200 && xhr.readyState == 4)
            {
                console.log(xhr.responseText);
                openDir(path);
            }
        }
        xhr.send("copy="+Copy+"&path="+encodeURIComponent(path)+"&old_path="+encodeURIComponent(mvPath)+"&files="+files+"&folders="+folders);
    }
}

var dl = function(file) {
    document.querySelector("#box").style.display="none";
    var id = 0;
    if(file.length > 1) {
        id = file.substr(1);
        if(isNumeric(id)) {
            if(file.substr(0, 1) == 'f') {
                location.href="User/Download/"+id;
            }
        }
    }
}

var rm = function(del) {
    document.querySelector("#box").style.display="none";
    var id = 0;
    if(del.length > 1) {
        id = del.substr(1);
        if(isNumeric(id)) {
            var xhr = new XMLHttpRequest();
            if(del.substr(0, 1) == 'f') {
                // file
                if(confirm(txt.User.questionf)) {
                    console.log("removing file");
                    xhr.open("POST", "User/RmFiles", true);
                    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

                    xhr.onreadystatechange = function()
                    {
                        if(xhr.status == 200 && xhr.readyState == 4)
                        {
                            openDir(path);
                        }
                    }
                    xhr.send("path="+encodeURIComponent(path)+"&files="+id);
                }
            }
            else if(del.substr(0, 1) == 'd') {
                // folder
                if(confirm(txt.User.questiond)) {
                    if(folderName = getFolderName(del)) {
                        xhr.open("POST", "User/RmFolders", true);
                        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

                        xhr.onreadystatechange = function()
                        {
                            if(xhr.status == 200 && xhr.readyState == 4)
                            {
                                openDir(path);
                            }
                        }
                        xhr.send("path="+encodeURIComponent(path)+"&folders="+encodeURIComponent(folderName));
                    }
                }
            }
        }
    }
}

var rmMultiple = function() {
    var id = 0;
    var folderName;
    //var rmFolders = [];
    //var rmFiles = [];
    if(SelectedFile.length > 0 || SelectedFolder.length > 0) {
        if(confirm(txt.User.questionrm)) {

            /*for(var i=0;i<selected.length;i++) {
                if(selected[i].length > 1) {
                    if(selected[i].substr(0, 1) == 'f') {
                        // file
                        id = selected[i].substr(1);
                        if(isNumeric(id))
                            rmFiles.push(id);
                    }
                    else if(selected[i].substr(0, 1) == 'd') {
                        // folder
                        if(folderName = getFolderName(selected[i]))
                            rmFolders.push(folderName);
                    }
                }
            }*/

            var wait = 2;
            if(SelectedFolder.length > 0) {
                var xhr = new XMLHttpRequest();
                console.log("deleting folders...");
                xhr.open("POST", "User/RmFolders", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

                xhr.onreadystatechange = function()
                {
                    if(xhr.status == 200 && xhr.readyState == 4)
                    {
                        if(xhr.responseText != '') {
                            //
                            wait--;
                            console.log(xhr.response);
                            console.log("deleted selected folders !");
                        }
                    }
                }
                xhr.send("path="+encodeURIComponent(path)+"&folders="+encodeURIComponent(SelectedFolder.join("|")));
            }
            else
                wait--;

            if(SelectedFile.length > 0) {
                var xhr2 = new XMLHttpRequest();
                console.log("deleting files...");
                xhr2.open("POST", "User/RmFiles", true);
                xhr2.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

                xhr2.onreadystatechange = function()
                {
                    if(xhr2.status == 200 && xhr2.readyState == 4)
                    {
                        if(xhr2.responseText != '') {
                            //
                            wait--;
                            console.log("deleted selected files !");
                        }
                    }
                }
                xhr2.send("path="+encodeURIComponent(path)+"&files="+encodeURIComponent(SelectedFile.join("|")));
            }
            else
                wait--;

            var timer = setInterval(function() {
                console.log("waiting...");
                if(wait == 0) {
                    clearInterval(timer);
                    openDir(path);
                }
            }, 250);
        }
    }
}
