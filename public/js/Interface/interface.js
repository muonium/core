/* interface.js : User's interface */
/*
    Modules :
- Box
- Arrows
- Selection
- Move
- Upload

*/

//      Global Vars     //

var returnArea;
var folder_id = 0; // Id of current folder
var trash = 0; // 0 : not in the trash, 1 : in the trash


// If trash = 0, set trash to 1 and load contents from trash.
// If trash = 1, set trash to 0 and load other contents.
var showTrashed = function() {
    trash = Math.abs(trash-1);
    if(trash == 0)
        document.querySelector("#button_trash").innerHTML = txt.User.trash_0;
    else
        document.querySelector("#button_trash").innerHTML = txt.User.trash_1;
    openDir(0);
}

//                  //
//      Modules     //
//                  //

// Box module. Loaded in window.onload()
var Box = (function() {
    // Private
    var box_div = document.querySelector("#box");
    var x = 0;
    var y = 0;
    //var Area = 0;

    // Public
    // Area : 0 : desktop div, 1 : file, 2 : folder
    return {
        Area : 0,

        hide : function() {
            box_div.style.display = 'none';
        },

        show : function() {
            box_div.style.display = 'block';
        },

        left_click : function(cx, cy) {
            // If the user uses left click inside the 'box'
            if((cx > x && cx < x + box_div.clientWidth) && (cy > y && cy < y + box_div.clientHeight)) {
                // Action
            }
            else { // Otherwise, hide 'box'
                Box.hide();
                Box.Area = 0;
            }
        },

        right_click : function(cx, cy, id) {
            // Show box at position x, y
            x = cx;
            y = cy;

            box_div.style.left = x+'px';
            box_div.style.top = y+'px';

            if(id === undefined) //when there isn't anything under the mouse
                Box.Area = 0;

            // Content according to area
            switch(Box.Area) {
                //over nothing
                case 0:
                    box_div.innerHTML = '<p onclick="nFolder()"><img src="'+img+'desktop/actions/create_folder.svg" class="icon"> '+txt.RightClick.nFolder+'</p><p onclick="Upload.dialog()"><img src="'+img+'desktop/actions/upload.svg" class="icon"> '+txt.RightClick.upFiles+'</p>';
                    if(Move.Files.length > 0 || Move.Folders.length > 0) { box_div.innerHTML += '<hr><p onclick="Move.paste(\''+id+'\')"><img src="'+img+'index/actions/paste.svg" class="icon"> '+txt.RightClick.paste+'</p>'; }
                    box_div.innerHTML += '<hr><p onclick="logout()">'+txt.RightClick.logOut+'</p>';
                    break;
                //mouse over a file
                case 1:
                    box_div.innerHTML = '<p onclick="dl(\''+id+'\')"><img src="'+img+'index/actions/download.svg" class="icon"> '+txt.RightClick.dl+'</p><hr><p><img src="'+img+'index/actions/putInFavorites.svg" class="icon"> '+txt.RightClick.star+'</p><hr><p onclick="Move.cut(\''+id+'\')"><img src="'+img+'index/actions/cut.svg" class="icon"> '+txt.RightClick.cut+'</p><p onclick="Move.copy(\''+id+'\')"><img src="'+img+'index/actions/copy.svg" class="icon"> '+txt.RightClick.copy+'</p><p onclick="Move.paste(\''+id+'\')"><img src="'+img+'index/actions/paste.svg" class="icon"> '+txt.RightClick.paste+'</p>';
                    if(trash == 0) { box_div.innerHTML += '<p onclick="Move.trash(\''+id+'\')"><img src="'+img+'index/actions/trash.svg" class="icon"> '+txt.RightClick.trash+'</p>'; }
                    else { box_div.innerHTML += '<p onclick="Move.trash(\''+id+'\')">'+txt.RightClick.restore+'</p><p onclick="rm(\''+id+'\')">'+txt.RightClick.rm+'</p>'; }
                    box_div.innerHTML += '<hr><p><img src="'+img+'index/actions/rename.svg" class="icon"> '+txt.RightClick.mvItem+'</p><p><img src="'+img+'index/actions/paste.svg" class="icon">'+txt.RightClick.mvLocate+'</p><hr><p>'+txt.RightClick.vDetails+'</p>';
                    break;
                //mouse over a folder
                case 2:
                    box_div.innerHTML = '<p onclick="openDir(\''+id+'\')"><img src="'+img+'index/actions/view.svg" class="icon"> '+txt.RightClick.open+'</p><hr><p onclick="Move.cut(\''+id+'\')"><img src="'+img+'index/actions/cut.svg" class="icon"> '+txt.RightClick.cut+'</p><p onclick="Move.copy(\''+id+'\')"><img src="'+img+'index/actions/copy.svg" class="icon"> '+txt.RightClick.copy+'</p><p onclick="Move.paste(\''+id+'\')"><img src="'+img+'index/actions/paste.svg" class="icon"> '+txt.RightClick.paste+'</p>';
                    if(trash == 0) { box_div.innerHTML += '<p onclick="Move.trash(\''+id+'\')"><img src="'+img+'index/actions/trash.svg" class="icon"> '+txt.RightClick.trash+'</p>'; }
                    else { box_div.innerHTML += '<p onclick="Move.trash(\''+id+'\')">'+txt.RightClick.restore+'</p><p onclick="rm(\''+id+'\')">'+txt.RightClick.rm+'</p>'; }
                    box_div.innerHTML += '<hr><p><img src="'+img+'index/actions/rename.svg" class="icon"> '+txt.RightClick.mvItem+'</p><p>'+txt.RightClick.mvLocate+'</p><hr><p>'+txt.RightClick.vDetails+'</p>';
            }
            Box.show();
        }
    }
});

// Arrows module. Loaded in window.onload()
var Arrows = (function() {
    // Private
    var lastSelected = '';
    var tree = null;
    var span = null;
    var max = 0;
    var i = 0;
    var init = false;

    // Public
    return {
        init : function() {
            tree = document.querySelector("#tree");
            if(tree === null)
                return false;
            span = tree.querySelectorAll("span");
            if(span === null || span.length === 0)
                return false;
            max = span.length-1;
            i = 0;
            lastSelected = '';
            init = true;
        },

        up : function(ctrl) {
            if(!init)
                return false;
            if(Selection.Files.length === 0 && Selection.Folders.length === 0 && lastSelected === '')
                i = max; // last element
            else if(i <= 0)
                i = max;
            else
                i--;
            lastSelected = span[i].id;

            if(ctrl === undefined) // remove previous selected element(s)
                Selection.remove();
            Selection.add(lastSelected);
        },

        down : function(ctrl) {
            if(!init)
                return false;
            if(Selection.Files.length === 0 && Selection.Folders.length === 0 && lastSelected === '')
                i = 0; // first element
            else if(i >= max)
                i = 0;
            else
                i++;
            lastSelected = span[i].id;

            if(ctrl === undefined) // remove previous selected element(s)
                Selection.remove();
            Selection.add(lastSelected);
        }
    }
});

// Upload module. Loaded in window.onload()
var Upload = (function() {
    // Private
    var xhr_upload = new Array();
    var filesUploaded = 0; // Number of files uploaded
    var progress = document.querySelector("#progress");

    // Public
    return {
        dialog : function() {
            document.querySelector('#upFilesInput').click();
        },

        abort : function(i) {
            document.querySelector("#div_upload"+i).style.display = 'none';
            xhr_upload[i].abort();
            console.log("aborted "+i);
            filesUploaded++;
        },

        upFiles : function(files) {
            // Upload multiple files function
            xhr_upload = new Array();
            // To change ?
            progress.innerHTML = ' ';
            // Loop through each of the selected files.
            for(var i=0;i<files.length;i++) {
                progress.innerHTML += '<div id="div_upload'+i+'"><button onclick="abort('+i+')">X</button> <span id="span_upload'+i+'"></span></div>';
                Upload.upFile(files[i], i);
            }

            // Waiting end of the uploading process
            var timer = setInterval(function() {
                console.log("waiting...");
                if(filesUploaded >= files.length) {
                    progress.innerHTML = ' ';
                    clearInterval(timer);
                    openDir(folder_id);
                }
            }, 1000);
        },

        upFile : function(file, i) {
            // Upload a file
            // Create a new FormData object.
            var formData = new FormData();

            // Add the file to the request.
            formData.append('folder_id', folder_id);
            formData.append('upload[]', file, file.name);
            xhr_upload[i] = new XMLHttpRequest();
            xhr_upload[i].open("POST", "User/UpFiles", true);

            // Progress bar
            xhr_upload[i].upload.addEventListener("progress", function(event, filename) {
                if(event.lengthComputable)
                    if(document.querySelector("#span_upload"+i))
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
    }
});

// Selection module. Loaded in window.onload()
var Selection = (function() {
    // Private

    // Public
    // addSel : 1 => add a new selection
    // Files : Selected files (id)
    // Folders : Selected folders (id)
    return {
        addSel : 0,
        Files : [],
        Folders : [],

        select : function(id) {
            document.querySelector("#"+id).style.backgroundColor='#E0F0FA';
        },

        unselect : function(id) {
            document.querySelector("#"+id).style.backgroundColor='white';
        },

        add : function(id) {
            if(id.length > 1) {
                if(id.substr(0, 1) == 'd')
                    Selection.addFolder(id);
                else if(id.substr(0, 1) == 'f')
                    Selection.addFile(id);
            }
        },

        addFile : function(id) {
            Selection.addSel = 1;
            if(document.querySelector("#"+id)) {
                var pos = Selection.Files.indexOf(id.substr(1));
                if(pos != -1) {
                    Selection.Files.splice(pos, 1);
                    Selection.unselect(id);
                }
                else {
                    Selection.Files.push(id.substr(1));
                    Selection.select(id);
                }
            }
        },

        addFolder : function(id) {
            Selection.addSel = 1;
            if(document.querySelector("#"+id)) {
                var pos = Selection.Folders.indexOf(id.substr(1));
                if(pos != -1) {
                    Selection.Folders.splice(pos, 1);
                    Selection.unselect(id);
                }
                else {
                    Selection.Folders.push(id.substr(1));
                    Selection.select(id);
                }
            }
        },

        invert : function() {
            Selection.addSel = 1; //
            var i = 0;
            var files = document.querySelectorAll(".file");
            for(i=0;i<files.length;i++)
                Selection.addFile(files[i].id);

            var folders = document.querySelectorAll(".folder");
            for(i=0;i<folders.length;i++)
                Selection.addFolder(folders[i].id);
        },

        all : function() {
            Selection.addSel = 1;
            var i = 0;
            var files = document.querySelectorAll(".file");
            for(i=0;i<files.length;i++) {
                if(document.querySelector("#"+files[i].id)) {
                    if(Selection.Files.indexOf((files[i].id).substr(1)) == -1) {
                        Selection.Files.push((files[i].id).substr(1));
                        Selection.select(files[i].id);
                    }
                }
            }

            var folders = document.querySelectorAll(".folder");
            for(i=0;i<folders.length;i++) {
                if(document.querySelector("#"+folders[i].id)) {
                    if(Selection.Folders.indexOf(folders[i].id.substr(1)) == -1) {
                        Selection.Folders.push(folders[i].id.substr(1));
                        Selection.select(folders[i].id);
                    }
                }
            }
        },

        remove : function() {
            for(var i=0;i<Selection.Files.length;i++)
                Selection.unselect("f"+Selection.Files[i]);
            for(var i=0;i<Selection.Folders.length;i++)
                Selection.unselect("d"+Selection.Folders[i]);
            Selection.Files = [];
            Selection.Folders = [];
        }
    }
});

// Move module. Loaded in window.onload()
var Move = (function() {
    // Private
    var Copy = 0; // 0 : cut, 1 : copy
    var mvFolder_id = 0; // folder id where files/folders to move are located

    var mv = function(id) {
        // reset
        Move.Files = [];
        Move.Folders = [];

        if(Selection.Files.length == 0 && Selection.Folders.length == 0) {
            // cut only the file/folder selected
            if(id.length > 0) {
                if(id.substr(0, 1) == 'd') {
                    Move.Folders.push(id.substr(1));
                }
                if(id.substr(0, 1) == 'f')
                    Move.Files.push(id.substr(1));
            }
        }
        else {
            // cut all selected files/folders
            Move.Files = Selection.Files;
            Move.Folders = Selection.Folders;
        }
        mvFolder_id = folder_id;
    }

    // Public
    // Files : Files to move (id)
    // Folders : Folders to move (id)
    return {
        Files : [],
        Folders : [],

        cut : function(id) {
            Box.hide();
            Copy = 0;

            mv(id);
        },

        copy : function(id) {
            Box.hide();
            Copy = 1;

            mv(id);
        },

        paste : function() {
            Box.hide();
            var id = 0;
            var folderName;

            var p_folders = [];
            var p_files = [];

            if(Move.Files.length > 0 || Move.Folders.length > 0) {
                for(var i=0; i<Move.Files.length; i++) {
                    if(Move.Files[i].length > 0 && isNumeric(Move.Files[i]))
                        p_files.push(Move.Files[i]);
                }

                for(var i=0; i<Move.Folders.length; i++) {
                    if(Move.Folders[i].length > 0 && isNumeric(Move.Folders[i]))
                        p_folders.push(Move.Folders[i]);
                }

                p_folders = encodeURIComponent(p_folders.join('|'));
                p_files = encodeURIComponent(p_files.join('|'));

                var xhr = new XMLHttpRequest();
                xhr.open("POST", "User/Mv", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

                xhr.onreadystatechange = function()
                {
                    if(xhr.status == 200 && xhr.readyState == 4)
                    {
                        console.log(xhr.responseText);
                        openDir(folder_id);
                    }
                }
                xhr.send("copy="+Copy+"&folder_id="+folder_id+"&old_folder_id="+mvFolder_id+"&files="+p_files+"&folders="+p_folders);
            }
        },

        trash : function(t) {
            Box.hide();
            if(t.length > 1) {
                id = t.substr(1);
                if(isNumeric(id)) {
                    var request;
                    if(t.substr(0, 1) == 'f') {
                        request = "files="+id;
                    }
                    else if(t.substr(0, 1) == 'd') {
                        request = "folders="+id;
                    }
                    else
                        return false;

                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "User/MvTrash", true);
                    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

                    xhr.onreadystatechange = function()
                    {
                        if(xhr.status == 200 && xhr.readyState == 4)
                        {
                            console.log(xhr.responseText);
                            openDir(folder_id);
                        }
                    }
                    xhr.send(request+"&trash="+Math.abs(trash-1));
                }
            }
        },

        trashMultiple : function() {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "User/MvTrash", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function()
            {
                if(xhr.status == 200 && xhr.readyState == 4)
                {
                    console.log(xhr.responseText);
                    openDir(folder_id);
                }
            }
            xhr.send("folders="+Selection.Folders.join('|')+"&files="+Selection.Files.join('|')+"&trash="+Math.abs(trash-1));
        }
    }
});

//                          //
//      End of modules      //
//                          //

//      App launcher

window.onload = function() {
    console.log("Loading application");

    // Get txt from user's language json (language.js)
    getJSON();

    /*
    */
    returnArea = document.querySelector("#returnArea");

    // Load modules
    Box = Box();
    Arrows = Arrows();
    Selection = Selection();
    Move = Move();
    Upload = Upload();

    // Set events in the app

    window.oncontextmenu = function(event) {
        // Disable right click
        return false;
    }

    window.onclick = function(event) {
        // Left click
        Box.left_click(event.clientX, event.clientY);
        // reset selected folders/files
        if(Selection.addSel == 0)
            Selection.remove();
        else
            Selection.addSel = 0;
    }

    window.addEventListener("keydown", function(event) {
        if(event.ctrlKey && event.keyCode == 68) {
            event.preventDefault(); // disable the hotkey in web browser
            logout();
        }
        else if(event.ctrlKey && event.keyCode == 65) {
            event.preventDefault(); // disable the hotkey in web browser
            selection.all();
        }
        else if(event.ctrlKey && event.keyCode == 73) {
            event.preventDefault(); // disable the hotkey in web browser
            Selection.invert();
        }
        else if(event.ctrlKey && event.keyCode == 82) {
            event.preventDefault(); // disable the hotkey in web browser
            Move.trashMultiple();
        }
        else if(event.ctrlKey && event.keyCode == 38) {
            event.preventDefault(); // disable the hotkey in web browser
            Arrows.up('ctrl');
        }
        else if(event.ctrlKey && event.keyCode == 40) {
            event.preventDefault(); // disable the hotkey in web browser
            Arrows.down('ctrl');
        }
        else {
            switch(event.keyCode) {
                case 46:
                    // suppr
                    rmMultiple();
                    break;
                case 27:
                    // esc
                    Box.hide();
                    break;
                case 38:
                    // up arrow
                    Arrows.up();
                    break;
                case 40:
                    // down arrow
                    Arrows.down();
                    break;
            }
        }
    });

    // Right click inside desktop section
    document.querySelector("#desktop").addEventListener("contextmenu", function(event) {
        //event.preventDefault();
        if(Box.Area == 0) // If we are inside desktop but not inside its children
            Box.right_click(event.clientX, event.clientY);
        else {
            // If we are inside its children, set Area to 0 because this function is always called when user call file's actions or folder's actions
            // Next, we will be able to use right click inside desktop div (area = 0) and when we
            // call file's actions or folder's actions, 'box' for 'desktop' area will not be displayed
            Box.Area = 0;
        }
        return false;
    });

    // Open root dir
    openDir(0);
}

//  Set events in files and folders
var setEvents = function() {
    // Init Arrows actions
    Arrows.init();

    // Right click inside divs with file's class (these divs are children of 'desktop')
    // After the execution of the function below, the function for 'desktop' above will be
    //called automatically (because we are inside desktop) and will set Area to 0 without displaying a new 'box'
    // Files actions
    var files = document.querySelectorAll(".file");
    for (var i = 0; i < files.length; i++) {
        // For each file
        files[i].addEventListener("contextmenu", function(event) {
            // Right click
            Box.Area = 1;
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
            Box.Area = 2;
            // Call right_click function with div's id
            Box.right_click(event.clientX, event.clientY, this.id);
            return false;
        });
    }
}

// Create a folder
var nFolder = function() {
    if(document.querySelector("#nFolder")) {
        Box.hide();

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "User/AddFolder", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function()
        {
            if(xhr.status == 200 && xhr.readyState == 4)
            {
                // Add the folder in tree without reloading
                if(isNumeric(xhr.response)) {
                    var first;
                    var content = '<img src="'+img+'desktop/extensions/folder.svg" class="icon"> <strong>'+document.querySelector("#nFolder").value+'</strong> [0]';
                    // Check if there is already file or folder
                    if(document.querySelector(".folder") || document.querySelector(".file")) {
                        if(!(first = document.querySelector(".folder")))
                            if(!(first = document.querySelector(".file")))
                                window.location.href="User";
                        var span = document.createElement('span');
                        span.className = 'folder';
                        span.id = 'd'+xhr.response;
                        span.name = document.querySelector("#nFolder").value;
                        span.onclick = function(){Selection.addFolder(this.id);};
                        span.ondblclick = function(){openDir(this.id.substr(1));};
                        span.innerHTML = content;
                        first.parentNode.insertBefore(span, first);
                    }
                    else {
                        var span = '<span class="folder" id="d'+xhr.response+'" name="'+document.querySelector("#nFolder").value+'" onclick="Selection.addFolder(this.id)" ondblclick="openDir('+xhr.response+')">'+content+'</span>';
                        document.querySelector("#tree").innerHTML = span + document.querySelector("#tree").innerHTML;
                    }

                    document.querySelector("#d"+xhr.response).addEventListener("contextmenu", function(event) {
                        Box.Area = 2;
                        Box.right_click(event.clientX, event.clientY, this.id);
                        return false;
                    });
                }
            }
        }
        xhr.send("folder_id="+folder_id+"&folder="+encodeURIComponent(document.querySelector("#nFolder").value));
    }
    else {
        document.querySelector("#box").innerHTML = txt.User.folder+' : <input type="text" id="nFolder" onkeypress="return verifFolderName(event);">';
    }
}

//      Open a folder

var openDir = function(id) {
    if(!isNumeric(id))
        return false;

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "User/ChangePath", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function()
    {
        if(xhr.status == 200 && xhr.readyState == 4)
        {
            if(xhr.responseText != '') {
                // Hide box
                Box.hide();

                document.querySelector("#mui").innerHTML = xhr.responseText;
                //path = dirName;
                folder_id = id;
                // reset selected files/folders
                Selection.Files = [];
                Selection.Folders = [];
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
    xhr.send("folder_id="+id+"&trash="+trash);
}

// Download a file
var dl = function(file) {
    Box.hide();
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

// Remove a file/folder
var rm = function(del) {
    Box.hide();
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
                            console.log(xhr.responseText);
                            openDir(folder_id);
                        }
                    }
                    //xhr.send("path="+encodeURIComponent(path)+"&files="+id);
                    xhr.send("folder_id="+folder_id+"&files="+id);
                }
            }
            else if(del.substr(0, 1) == 'd') {
                // folder
                if(confirm(txt.User.questiond)) {
                    xhr.open("POST", "User/RmFolders", true);
                    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

                    xhr.onreadystatechange = function()
                    {
                        if(xhr.status == 200 && xhr.readyState == 4)
                        {
                            console.log(xhr.responseText);
                            openDir(folder_id);
                        }
                    }
                    xhr.send("folder_id="+folder_id+"&folders="+id);
                }
            }
        }
    }
}

// Remove files/folders
var rmMultiple = function() {
    var id = 0;
    var folderName;

    if(Selection.Files.length > 0 || Selection.Folders.length > 0) {
        if(confirm(txt.User.questionrm)) {
            var wait = 2;
            if(Selection.Folders.length > 0) {
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
                            console.log(xhr.responseText);
                            console.log("deleted selected folders !");
                        }
                    }
                }
                xhr.send("folder_id="+folder_id+"&folders="+encodeURIComponent(Selection.Folders.join("|")));
            }
            else
                wait--;

            if(Selection.Files.length > 0) {
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
                            console.log(xhr2.responseText);
                            console.log("deleted selected files !");
                        }
                    }
                }
                xhr2.send("folder_id="+folder_id+"&files="+encodeURIComponent(Selection.Files.join("|")));
            }
            else
                wait--;

            var timer = setInterval(function() {
                console.log("waiting...");
                if(wait == 0) {
                    clearInterval(timer);
                    openDir(folder_id);
                }
            }, 250);
        }
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

var reset = function() {
    this.value = '';
}

var getFolderName = function(id) {
    if(document.getElementById(id))
        return document.getElementById(id).getAttribute("name");
    return false;
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
