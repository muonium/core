/* interface.js : User's interface */
/*
    Modules (modules folder) :
- Box
- Arrows
- Selection
- Move      (Move files and folders, also from/to trash)
- Upload
- Files
- Folders
- Rm        (Remove files and folders)
- Trash
- Favorites
- ExtIcons  (Icons according to extensions)
*/

//      Global Vars     //
var returnArea;

//                  	//
//      Modules     	//
// => in modules folder //

//      App launcher

var UserLoader = function(folder_id) {
    console.log("Loading application.");

    // Get txt from user's language json (language.js)
    getJSON();

    returnArea = document.querySelector("#returnArea");

    console.log(Request.modulesLoaded);
    if(Request.modulesLoaded === undefined || Request.modulesLoaded === false) {
        // Load modules
        console.log("Loading modules.");
        Box = Box();
        Arrows = Arrows();
		Decryption = Decryption();
		Encryption = Encryption();
        Selection = Selection();
        Move = Move();
        Upload = Upload();
    	Files = Files();
    	Folders = Folders();
		Time = Time();
    	Trash = Trash();
    	Rm = Rm();
        Favorites = Favorites();
    	ExtIcons = ExtIcons();
        Request.modulesLoaded = true;
    }
    else {
        if(Trash.State === 1)
            document.querySelector("#button_trash").innerHTML = txt.User.trash_1;
        console.log("Modules already loaded.");
    }

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
            Selection.all();
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
        else if(event.ctrlKey && event.keyCode == 67) {
            event.preventDefault(); // disable the hotkey in web browser
            Move.copy();
        }
        else if(event.ctrlKey && event.keyCode == 88) {
            event.preventDefault(); // disable the hotkey in web browser
            Move.cut();
        }
        else if(event.ctrlKey && event.keyCode == 86) {
            event.preventDefault(); // disable the hotkey in web browser
            Move.paste();
        }
        else if(event.ctrlKey && event.keyCode == 83) {
            event.preventDefault(); // disable the hotkey in web browser
            if(Selection.Files.length > 0) {
                // Start download for one file per second
                var i = 0;
                var timer = setInterval(function() {
                    Files.dl("f"+Selection.Files[i]);
                    i++;
                    if(i >= Selection.Files.length)
                        clearInterval(timer);
                }, 1000);
            }
        }
        else {
            switch(event.keyCode) {
                case 8:
                    // backspace
					if(document.activeElement.tagName != 'INPUT' && document.activeElement.tagName != 'TEXTAREA') {
                    	event.preventDefault();
                    	Folders.back();
					}
                    break;
                case 46:
                    // suppr
					if(document.activeElement.tagName != 'INPUT' && document.activeElement.tagName != 'TEXTAREA') {
                    	Rm.multiple();
					}
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
                case 13:
                    // enter
					if(document.activeElement.tagName != 'INPUT' && document.activeElement.tagName != 'TEXTAREA') {
	                    if(Selection.Files.length == 1 && Selection.Folders.length == 0)
	                        Files.dl("f"+Selection.Files[0]);
	                    else if(Selection.Files.length == 0 && Selection.Folders.length == 1)
	                        Folders.open(Selection.Folders[0]);
					}
                    break;
            }
        }
    });

    // Right click inside desktop section
    document.querySelector("#desktop").addEventListener("contextmenu", function(event) {
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

    // Open specified dir or root dir
    if(folder_id === undefined || !isNumeric(folder_id))
        Folders.open(0);
    else
        Folders.open(folder_id);

    console.log("Application loaded.");
}

//  Set events in files and folders
var setEvents = function() {
    // Init Arrows actions
    Arrows.init();

    // Init Box
    Box.init();

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

var reset = function() {
    this.value = '';
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
