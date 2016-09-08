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

var Move = ''; // Var which contains file/folder id cut/copied
var Copy = 0; // 0 : cut, 1 : copy

var selected = []; // selected files/folders

var path = ''; // Current path

var filesUploaded = 0; // Number of files uploaded

var returnArea;

// Box class. Show a div 'box' when user uses right click inside desktop div, close the box when user uses left click

var box = class {

    constructor() {
        this.box_div = document.querySelector("#box");
    }

    left_click(x, y) {
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

    right_click(x, y, id) {
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
                this.box_div.innerHTML = '<p onclick="nFolder()">'+txt.RightClick.nFolder+'</p><p onclick="upFilesDialog()">'+txt.RightClick.upFiles+'</p><hr><p onclick="logout()">'+txt.RightClick.logOut+'</p>';
                break;
            //mouse over a file
            case 1:
                this.box_div.innerHTML = '<p>'+txt.RightClick.dl+'</p><hr><p>'+txt.RightClick.star+'</p><hr><p onclick="cut(\''+id+'\')">'+txt.RightClick.cut+'</p><p onclick="copy(\''+id+'\')">'+txt.RightClick.copy+'</p><p onclick="paste(\''+id+'\')">'+txt.RightClick.paste+'</p><p onclick="rm(\''+id+'\')">'+txt.RightClick.rm+'</p><hr><p>'+txt.RightClick.mvItem+'</p><p>'+txt.RightClick.mvLocate+'</p><hr><p>'+txt.RightClick.vDetails+'</p>';
                break;
            //mouse over a folder
            case 2:
                this.box_div.innerHTML = '<p onclick="openDirById(\''+id+'\')">'+txt.RightClick.open+'</p><hr><p onclick="cut(\''+id+'\')">'+txt.RightClick.cut+'</p><p onclick="copy(\''+id+'\')">'+txt.RightClick.copy+'</p><p onclick="paste(\''+id+'\')">'+txt.RightClick.paste+'</p><p onclick="rm(\''+id+'\')">'+txt.RightClick.rm+'</p><hr><p>'+txt.RightClick.mvItem+'</p><p>'+txt.RightClick.mvLocate+'</p><hr><p>'+txt.RightClick.vDetails+'</p>';
        }
        this.box_div.style.display = 'block';
    }
};

window.oncontextmenu = function(event) {
    // Disable right click
    return false;
}

window.onclick = function(event) {
    // Left click
    Box.left_click(event.clientX, event.clientY);
    // reset selected folders/files
    if(addSel == 0) {
        for(var i=0;i<selected.length;i++) {
            document.querySelector("#"+selected[i]).style.backgroundColor="white";
        }
        selected = [];
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
    console.log(p);
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
        xhr.open("POST", "User/addFolder", true);
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
        console.log(path);
        xhr.send("path="+encodeURIComponent(path)+"&folder="+encodeURIComponent(document.querySelector("#nFolder").value));
        
        // Refresh
        
        // Hide box
        //document.querySelector("#box").style.display = 'none';
    }
    else {
        document.querySelector("#box").innerHTML = 'Folder name : <input type="text" id="nFolder" onkeypress="return verifFolderName(event);">';
    }
}

var verifFolderName = function(evt) {
    var keyCode = evt.which ? evt.which : evt.keyCode;
    if(keyCode == 13) { // Submit
        nFolder();
        return false;
    }
    var interdit = '/\\:*?<>|" ';
    if (interdit.indexOf(String.fromCharCode(keyCode)) >= 0)
        return false;
    return true;
}

var upFilesDialog = function() {
    document.querySelector('#upFilesInput').click();
}

var upFiles = function(files) {
    // Upload multiple files function
    
    var progress = document.querySelector("#progress");
    progress.innerHTML = ' ';
    
    // Loop through each of the selected files.
    for(var i=0;i<files.length;i++) {
        progress.innerHTML += '<div id="upload'+i+'"></div>';
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
    var xhr = new Array();
    
    // Create a new FormData object.
    var formData = new FormData();

    // Add the file to the request.
    formData.append('path', path);
    formData.append('upload[]', file, file.name);
    xhr[i] = new XMLHttpRequest();
    xhr[i].open("POST", "User/upFiles", true);
        
    // Progress bar
    xhr[i].upload.addEventListener("progress", function(event, filename) {
        if(event.lengthComputable)
            document.querySelector("#upload"+i).innerHTML = file.name+" : "+(event.loaded/event.total*100).toFixed(2)+"%";
    }, false);
        
    xhr[i].onreadystatechange = function() {
        if(xhr[i].readyState === 4) {
            if(xhr[i].status === 200) {
                filesUploaded++;
            }
        }
    };
    xhr[i].send(formData);
}

var getFolderName = function(id) {
    if(document.getElementById(id))
        return document.getElementById(id).getAttribute("name");
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
    xhr.open("POST", "User/changePath", true);
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
                selected = [];
                // Set events for all files and folders loaded
                setEvents();
            }
        }
    }
    xhr.send("path="+encodeURIComponent(dirName));
}

var addSelection = function(id) {
    addSel = 1;
    if(document.querySelector("#"+id)) {
        var pos = selected.indexOf(id);
        if(pos != -1) {
            selected.splice(pos, 1);
            document.querySelector("#"+id).style.backgroundColor='white';
        }
        else {
            selected.push(id);
            document.querySelector("#"+id).style.backgroundColor='#E0F0FA';
        }
    }
}

var cut = function(id) {
    Move = id;
    Copy = 0;
}

var copy = function(id) {
    Move = id;
    Copy = 1;
}

var paste = function() {
    var id = 0;
    if(Move.length > 1) {
        id = Move.substr(1);
        if(isNumeric(id)) {
            if(Move.substr(0, 1) == 'f') {
                // file
            }
            else if(Move.substr(0, 1) == 'd') {
                // folder
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
                if(confirm("Do you want to remove this file ?")) {
                    xhr.open("POST", "User/rmFiles", true);
                    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

                    xhr.onreadystatechange = function()
                    {
                        if(xhr.status == 200 && xhr.readyState == 4)
                        {              
                            openDir(path);
                        }
                    }
                    xhr.send("path="+path+"&files="+id);
                }
            }
            else if(del.substr(0, 1) == 'd') {
                // folder
                if(confirm("Do you want to remove this folder ?")) {
                    if(folderName = getFolderName(id)) {
                        xhr.open("POST", "User/rmFolders", true);
                        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

                        xhr.onreadystatechange = function()
                        {
                            if(xhr.status == 200 && xhr.readyState == 4)
                            {              
                                openDir(path);
                            }
                        }
                        xhr.send("path="+path+"&folders="+encodeURIComponent(folderName));
                    }
                }
            }
        }
    }
}

var rmMultiple = function() {
    var id = 0;
    var folderName;
    var rmFolders = [];
    var rmFiles = [];
    if(selected.length > 0) {
        if(confirm("Do you want to remove these files/folders ?")) {
            
            for(var i=0;i<selected.length;i++) {
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
            }
            
            var wait = 2;
            if(rmFolders.length > 0) {
                var xhr = new XMLHttpRequest();
                console.log("deleting folders...");
                xhr.open("POST", "User/rmFolders", true);
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
                xhr.send("path="+path+"&folders="+encodeURIComponent(rmFolders.join("|")));
            }
            else
                wait--;
            
            if(rmFiles.length > 0) {
                var xhr2 = new XMLHttpRequest();
                console.log("deleting files...");
                xhr2.open("POST", "User/rmFiles", true);
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
                xhr2.send("path="+path+"&files="+encodeURIComponent(rmFiles.join("|")));
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