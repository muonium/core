/* interface.js : User's interface */

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

    //console.log(Request.modulesLoaded);
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
        MessageBox = MessageBox();
        Toolbar = Toolbar();
        Transfers = Transfers();
        Request.modulesLoaded = true;
    }
    else {
        if(Trash.state === 1) $("#button_trash").html(txt.User.trash_1);
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
        if(Selection.addSel == 0) {
            Selection.remove();
            Selection.closeDetails();
        }
        else
            Selection.addSel = 0;
    }

    window.addEventListener("keydown", function(event) {
        if(event.ctrlKey && event.keyCode == 68) { // CTRL + D
            event.preventDefault(); // disable the hotkey in web browser
            logout();
        }
        else if(event.ctrlKey && event.keyCode == 65) { // CTRL + A
            event.preventDefault(); // disable the hotkey in web browser
            Selection.all();
        }
        else if(event.ctrlKey && event.keyCode == 73) { // CTRL + I
            event.preventDefault(); // disable the hotkey in web browser
            Selection.invert();
        }
        else if(event.ctrlKey && event.keyCode == 82) { // CTRL + R
            event.preventDefault(); // disable the hotkey in web browser
            Move.trashMultiple();
        }
        else if(event.ctrlKey && event.keyCode == 38) { // CTRL + Arrow Up
            event.preventDefault(); // disable the hotkey in web browser
            Arrows.up('ctrl');
        }
        else if(event.ctrlKey && event.keyCode == 40) { // CTRL + Arrow down
            event.preventDefault(); // disable the hotkey in web browser
            Arrows.down('ctrl');
        }
        else if(event.ctrlKey && event.keyCode == 67) { // CTRL + C
            event.preventDefault(); // disable the hotkey in web browser
            Move.copy();
        }
        else if(event.ctrlKey && event.keyCode == 88) { // CTRL + X
            event.preventDefault(); // disable the hotkey in web browser
            Move.cut();
        }
        else if(event.ctrlKey && event.keyCode == 86) { // CTRL + V
            event.preventDefault(); // disable the hotkey in web browser
            Move.paste();
        }
        else if(event.ctrlKey && event.keyCode == 83) { // CTRL + S
            event.preventDefault(); // disable the hotkey in web browser
            if(Selection.Files.length > 0) {
                // Start download for one file per second
                Selection.dl();
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
                    // delete
					if(document.activeElement.tagName != 'INPUT' && document.activeElement.tagName != 'TEXTAREA') {
                    	Rm.multiple();
					}
                    break;
                case 27:
                    // esc
                    Box.hide();
                    if($('#MessageBox').length && $('#MessageBox').css('display') == 'block') { $('#MessageBox').remove(); }
                    break;
                case 38:
                    // up arrow
                    Arrows.up();
                    event.preventDefault();
                    break;
                case 40:
                    // down arrow
                    Arrows.down();
                    event.preventDefault();
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
                case 112:
                    // f1
                    showHelp();
                    event.preventDefault();
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

    Toolbar.display();

    document.querySelector("#multisel").addEventListener("click", function() { Selection.multipleSwitch('multisel'); });

    document.querySelector("#display_list").addEventListener("click", function() {
        localStorage.setItem('display', 'list');
        Files.style = 'list';
        Files.display();
    });

    document.querySelector("#display_mosaic").addEventListener("click", function() {
        localStorage.setItem('display', 'mosaic');
        Files.style = 'mosaic';
        Files.display();
    });

    var display = localStorage.getItem('display');
    if(display == 'list' || display == 'mosaic') {
        document.querySelector("#display_"+display).click();
    }

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

    // Dragbars
    var dragbars = document.querySelectorAll(".dragbar");
    document.querySelector("div#container").onmouseup = function(event) {
        document.querySelector("div#container").onmousemove = null;
    };
    for(var i = 0; i < dragbars.length; i++) {
        var min = dragbars[i].parentNode.clientWidth;
        dragbars[i].addEventListener("mousedown", function(event) {
            var me = this;
            event.preventDefault();
            window.onresize = function() {
                me.parentNode.style['min-width'] = min + 'px';
            };
            document.querySelector("div#container").onmousemove = function(event) {
                var max = document.body.clientWidth - 175;
                var pos = event.pageX + 2;
                if(pos < min) pos = min;
                if(pos > max) pos = max;
                me.parentNode.style['min-width'] = pos + 'px';
            };
        });
    }

    var transfers_circles = document.querySelectorAll(".transfers-circle");
    Transfers.watch('number', function(prop, oldval, newval) { // watch and trigger property changes thanks to Object.prototype.watch() and object-watch.js polyfill
        for(var i = 0; i < transfers_circles.length; i++) {
            transfers_circles[i].innerHTML = newval;
        }
        return newval;
    });
    var transfers_up_circles = document.querySelectorAll(".transfers-up-circle");
    Transfers.watch('numberUp', function(prop, oldval, newval) { // watch and trigger property changes thanks to Object.prototype.watch() and object-watch.js polyfill
        for(var i = 0; i < transfers_up_circles.length; i++) {
            transfers_up_circles[i].innerHTML = newval;
        }
        return newval;
    });
    var transfers_dl_circles = document.querySelectorAll(".transfers-dl-circle");
    Transfers.watch('numberDl', function(prop, oldval, newval) { // watch and trigger property changes thanks to Object.prototype.watch() and object-watch.js polyfill
        for(var i = 0; i < transfers_dl_circles.length; i++) {
            transfers_dl_circles[i].innerHTML = newval;
        }
        return newval;
    });
}

var reset = function() {
    this.value = '';
}

var isNumeric = function(n) {
    if(typeof(n) === "string") n = n.replace(",", ".");
    return !isNaN(parseFloat(n)) && isFinite(n);
}

var cleanPath = function(p) {
    // format : dir1/dir2/
    if(p == '/') return '';
    if(p.length > 1) {
        if(p.substr(0, 1) === '/') {
			p = p.substr(1);
		}
        var p0 = p.split("/");
        for(var i = 0; i < p0.length; i++) {
            if(p0[i] === '') {
                p0.splice(i, 1);
			}
		}
        p = p0.join('/');
        if(p.substr(-1) !== '/') {
            p = p+'/';
		}
    }
    return p;
}

var showHelp = function() {
    var m = new MessageBox(txt.Help.shortcuts.join('\n')).show();
}

var logout = function() {
	sessionStorage.clear();
    window.location.href=ROOT+"Logout";
    return false;
}
