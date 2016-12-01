/* interface.js : User's interface */
/*
    Modules :
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

//                  //
//      Modules     //
//                  //

// Box module. Loaded in window.onload()
var Box = (function() {
    // Private
    var box_div = null;
    var x = 0;
    var y = 0;
    var init = false;

    // Public
    // Area : 0 : desktop div, 1 : file, 2 : folder
    // box_more : used in "show details" feature, to avoid default behavior (hide the box)
    return {
        Area : 0,
        box_more : false,

        init : function() {
            box_div = document.querySelector("#box");
            init = true;
        },

        hide : function() {
            //console.log("box hide");
            if(!init)
                return false;
            box_div.style.display = 'none';
        },

        show : function() {
            //console.log("box show");
            if(!init)
                return false;
            box_div.style.display = 'block';
        },

        reset : function() {
            //console.log("box reset");
            if(!init)
                return false;
            box_div.innerHTML = ' ';
        },

        set : function(content) {
            //console.log("box set");
            if(!init)
                return false;
            box_div.innerHTML = content;
        },

        left_click : function(cx, cy) {
            if(!init)
                return false;
            // If the user uses left click inside the 'box'
            if((cx >= x && cx <= (x + box_div.clientWidth)) && (cy >= y && cy <= (y + box_div.clientHeight)) || Box.box_more) {
                //console.log("left click action");
                // Action
                Box.box_more = false;
            }
            else { // Otherwise, hide 'box'
                //console.log("left click hide box");
                Box.hide();
                Box.Area = 0;
            }
        },

        right_click : function(cx, cy, id) {
            if(!init)
                return false;
            // Show box at position x, y
            //console.log("right click");
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
                    if(Trash.State == 0) {
                        box_div.innerHTML = '<p onclick="Folders.create()"><img src="'+img+'desktop/actions/create_folder.svg" class="icon"> '+txt.RightClick.nFolder+'</p><p onclick="Upload.dialog()"><img src="'+img+'desktop/actions/upload.svg" class="icon"> '+txt.RightClick.upFiles+'</p>';
                        if(Move.Files.length > 0 || Move.Folders.length > 0)
                            box_div.innerHTML += '<hr><p onclick="Move.paste(\''+id+'\')"><img src="'+img+'index/actions/paste.svg" class="icon"> '+txt.RightClick.paste+'</p>';
                        box_div.innerHTML += '<hr><p onclick="logout()">'+txt.RightClick.logOut+'</p>';
                    }
                    break;
                //mouse over a file
                case 1:
                    box_div.innerHTML = '<p onclick="Files.dl(\''+id+'\')"><img src="'+img+'index/actions/download.svg" class="icon"> '+txt.RightClick.dl+'</p><hr>';
                    if(Trash.State == 0) {
                        box_div.innerHTML += '<p onclick="Favorites.update(\''+id+'\')"><img src="'+img+'index/actions/putInFavorites.svg" class="icon"> '+txt.RightClick.star+'</p><hr><p onclick="Move.cut(\''+id+'\')"><img src="'+img+'index/actions/cut.svg" class="icon"> '+txt.RightClick.cut+'</p><p onclick="Move.copy(\''+id+'\')"><img src="'+img+'index/actions/copy.svg" class="icon"> '+txt.RightClick.copy+'</p><p onclick="Move.paste(\''+id+'\')"><img src="'+img+'index/actions/paste.svg" class="icon"> '+txt.RightClick.paste+'</p>';
                        box_div.innerHTML += '<p onclick="Move.trash(\''+id+'\')"><img src="'+img+'index/actions/trash.svg" class="icon"> '+txt.RightClick.trash+'</p>';
                    } else { box_div.innerHTML += '<p onclick="Move.trash(\''+id+'\')">'+txt.RightClick.restore+'</p><p onclick="Rm.rm(\''+id+'\')">'+txt.RightClick.rm+'</p>'; }
                    if(Trash.State == 0)
                        box_div.innerHTML += '<hr><p onclick="Move.rename(\''+id+'\')"><img src="'+img+'index/actions/rename.svg" class="icon"> '+txt.RightClick.mvItem+'</p><p><img src="'+img+'index/actions/paste.svg" class="icon">'+txt.RightClick.mvLocate+'</p>';
                    box_div.innerHTML += '<hr><p onclick="Files.details(\''+id+'\')">'+txt.RightClick.vDetails+'</p>';
                    break;
                //mouse over a folder
                case 2:
                    box_div.innerHTML = '<p onclick="Folders.open(\''+id.substr(1)+'\')"><img src="'+img+'index/actions/view.svg" class="icon"> '+txt.RightClick.open+'</p><hr>';
                    if(Trash.State == 0) {
                        box_div.innerHTML += '<p onclick="Move.cut(\''+id+'\')"><img src="'+img+'index/actions/cut.svg" class="icon"> '+txt.RightClick.cut+'</p><p onclick="Move.copy(\''+id+'\')"><img src="'+img+'index/actions/copy.svg" class="icon"> '+txt.RightClick.copy+'</p><p onclick="Move.paste(\''+id+'\')"><img src="'+img+'index/actions/paste.svg" class="icon"> '+txt.RightClick.paste+'</p>';
                        box_div.innerHTML += '<p onclick="Move.trash(\''+id+'\')"><img src="'+img+'index/actions/trash.svg" class="icon"> '+txt.RightClick.trash+'</p>';
                    } else { box_div.innerHTML += '<p onclick="Move.trash(\''+id+'\')">'+txt.RightClick.restore+'</p><p onclick="Rm.rm(\''+id+'\')">'+txt.RightClick.rm+'</p>'; }
                    if(Trash.State == 0)
                        box_div.innerHTML += '<hr><p onclick="Move.rename(\''+id+'\')"><img src="'+img+'index/actions/rename.svg" class="icon"> '+txt.RightClick.mvItem+'</p><p>'+txt.RightClick.mvLocate+'</p>';
                    box_div.innerHTML += '<hr><p onclick="Folders.details(\''+id+'\')">'+txt.RightClick.vDetails+'</p>';
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
            document.querySelector("#progress").innerHTML = ' ';
            // Loop through each of the selected files.
            for(var i=0;i<files.length;i++) {
                document.querySelector("#progress").innerHTML += '<div id="div_upload'+i+'"><button onclick="Upload.abort('+i+')">X</button> <span id="span_upload'+i+'"></span></div>';
                Upload.upFile(files[i], i);
            }

            // Waiting end of the uploading process
            var timer = setInterval(function() {
                console.log("waiting...");
                if(filesUploaded >= files.length) {
                    document.querySelector("#progress").innerHTML = ' ';
                    clearInterval(timer);
                    Folders.open(Folders.id);
                }
            }, 1000);
        },

        upFile : function(file, i) {
            // Upload a file
            // Create a new FormData object.
            var formData = new FormData();

            // Add the file to the request.
            formData.append('folder_id', Folders.id);
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
        mvFolder_id = Folders.id;
    }

    // Public
    // Files : Files to move (id)
    // Folders : Folders to move (id)
    return {
        Files : [],
        Folders : [],

        rename : function(id) {
            var elem = document.querySelector("#"+id);
            var name;
            var path;
            if(elem) {
                if(elem.hasAttribute("data-path")) {
                    path = elem.getAttribute("data-path");
                    if(elem.hasAttribute("data-title"))
                        name = elem.getAttribute("data-title");
                    else
                        name = elem.getAttribute("name");

                    if(document.querySelector("#nRename")) {
                        console.log("rename");
        				Box.hide();

        				var xhr = new XMLHttpRequest();
        				xhr.open("POST", "User/Rename", true);
        				xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        				xhr.onreadystatechange = function()
        				{
        				    if(xhr.status == 200 && xhr.readyState == 4)
        				    {
        				        // To do : rename without reloading
                                console.log(xhr.responseText);
                                Folders.open(Folders.id);
        				    }
        				}
        				xhr.send("path="+path+"&old="+encodeURIComponent(name)+"&new="+encodeURIComponent(document.querySelector("#nRename").value));
        			}
        			else {
                        Box.box_more = true;
        				document.querySelector("#box").innerHTML = txt.RightClick.mvItem+' : <input type="text" id="nRename" value="'+name+'" autocomplete="off" onkeypress="return Move.renameVerif(\''+id+'\', event);" autofocus>';
                        document.querySelector("#nRename").focus();
                        Box.show();
        			}
                }
            }
        },

        renameVerif : function(id, evt) {
			var keyCode = evt.which ? evt.which : evt.keyCode;
			if(keyCode == 13) { // Submit
				Move.rename(id);
				return false;
			}
			var interdit = '/\\:*?<>|"';
			if (interdit.indexOf(String.fromCharCode(keyCode)) >= 0)
				return false;
			return true;
		},

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
                        Folders.open(Folders.id);
                    }
                }
                xhr.send("copy="+Copy+"&folder_id="+Folders.id+"&old_folder_id="+mvFolder_id+"&files="+p_files+"&folders="+p_folders);
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
                            Folders.open(Folders.id);
                        }
                    }
                    xhr.send(request+"&trash="+Math.abs(Trash.State-1));
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
                    Folders.open(Folders.id);
                }
            }
            xhr.send("folders="+Selection.Folders.join('|')+"&files="+Selection.Files.join('|')+"&trash="+Math.abs(Trash.State-1));
        }
    }
});

// Files module. Loaded in window.onload()
var Files = (function() {
	// Private

	// Public
	return {
		dl : function(file) {
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
		},

        details : function(el) {
            var elem;
            if(elem = document.querySelector("#"+el)) {
                Box.box_more = true;
                Box.reset();
                Box.Area = 1;
                Box.set("<p style='padding:5px'>\
                <button onclick=\"Box.right_click(event.clientX, event.clientY, '"+el+"')\"><</button> &nbsp;&nbsp;<strong>Details</strong>\
                <hr><ul><li>"+txt.User.name+" : "+elem.getAttribute("data-title")+"</li>\
                <li>"+txt.User.path+" : "+elem.getAttribute("data-path")+"/</li>\
                <li>"+txt.User.type+" : "+txt.User.file+" <span class='ext_icon'></span></li>\
                <li>"+txt.User.size+" : "+elem.innerHTML.substr(elem.innerHTML.lastIndexOf("["))+"</li>\
                <li>"+elem.title+"</li></ul></p>");

                var newNode = document.importNode(elem.getElementsByTagName('img')[0], true);
                document.querySelector(".ext_icon").appendChild(newNode);
                Box.show();
            }
        }
	}
});

// Folders module. Loaded in window.onload()
var Folders = (function() {
	// Private

	// Public
	return {
		id : 0,
		create : function() {
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
				                span.onclick = function(){ Selection.addFolder(this.id); };
				                span.ondblclick = function(){ Folders.open(this.id.substr(1)); };
				                span.innerHTML = content;
				                first.parentNode.insertBefore(span, first);
				            }
				            else {
				                var span = '<span class="folder" id="d'+xhr.response+'" name="'+document.querySelector("#nFolder").value+'" onclick="Selection.addFolder(this.id)" ondblclick="Folders.open('+xhr.response+')">'+content+'</span>';
				                document.querySelector("#tree").innerHTML = span + document.querySelector("#tree").innerHTML;
				            }

				            document.querySelector("#d"+xhr.response).addEventListener("contextmenu", function(event) {
				                Box.Area = 2;
				                Box.right_click(event.clientX, event.clientY, this.id);
				                return false;
				            });

                            Arrows.init();
				        }
				    }
				}
				xhr.send("folder_id="+Folders.id+"&folder="+encodeURIComponent(document.querySelector("#nFolder").value));
			}
			else {
				document.querySelector("#box").innerHTML = txt.User.foldername+' : <input type="text" id="nFolder" autocomplete="off" onkeypress="return Folders.verif(event);" autofocus>';
                document.querySelector("#nFolder").focus();
			}
		},

		open : function(folder_id) {
			if(!isNumeric(folder_id))
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
				        Folders.id = folder_id;
				        // reset selected files/folders
				        Selection.Files = [];
				        Selection.Folders = [];
				        // Set events for all files and folders loaded
				        setEvents();

				        // Put icons
						ExtIcons.set();
				    }
				}
			}
			xhr.send("folder_id="+folder_id+"&trash="+Trash.State);
		},

        back : function() {
            // Open parent folder
            var parent = document.querySelector("a[id^=parent-]");
            if(parent)
                Folders.open(parent.id.substr(parent.id.lastIndexOf("-")+1));
        },

		verif : function(evt) {
			var keyCode = evt.which ? evt.which : evt.keyCode;
			if(keyCode == 13) { // Submit
				Folders.create();
				return false;
			}
			var interdit = '/\\:*?<>|"';
			if (interdit.indexOf(String.fromCharCode(keyCode)) >= 0)
				return false;
			return true;
		},

		getName : function(folder_id) {
			if(document.getElementById(folder_id))
				return document.getElementById(folder_id).getAttribute("name");
			return false;
		},

        getDataFolder : function(elem_id) {
            if(document.getElementById(elem_id))
                return document.getElementById(elem_id).getAttribute("data-folder");
            return false;
        },

        details : function(el) {
            var elem;
            if(elem = document.querySelector("#"+el)) {
                Box.box_more = true;
                Box.reset();
                Box.Area = 2;

                Box.set("<p style='padding:5px'>\
                <button onclick=\"Box.right_click(event.clientX, event.clientY, '"+el+"')\"><</button> &nbsp;&nbsp;<strong>Details</strong>\
                <hr><ul><li>"+txt.User.name+" : "+elem.getAttribute("name")+"</li>\
                <li>"+txt.User.path+" : "+elem.getAttribute("data-path")+"/</li>\
                <li>"+txt.User.type+" : "+txt.User.folder+" <span class='ext_icon'></span></li>\
                <li>"+txt.User.size+" : "+elem.innerHTML.substr(elem.innerHTML.lastIndexOf("["))+"</li>\
                </ul></p>");

                var newNode = document.importNode(elem.getElementsByTagName('img')[0], true);
                document.querySelector(".ext_icon").appendChild(newNode);
                Box.show();
            }
        }
	}
});

var Trash = (function() {
	// Private

	// Public
	// If State = 0, set State to 1 and load contents from trash.
	// If State = 1, set State to 0 and load other contents.
	return {
		State : 0,
		switch : function() {
			Trash.State = Math.abs(Trash.State-1);
			if(Trash.State == 0)
				document.querySelector("#button_trash").innerHTML = txt.User.trash_0;
			else
				document.querySelector("#button_trash").innerHTML = txt.User.trash_1;
			Folders.open(0);
		}
	}
});

var Rm = (function() {
	// Private

	// Public
	return {
		rm : function(del) {
			Box.hide();
			var id = 0;
			if(del.length > 1) {
				id = del.substr(1);
				if(isNumeric(id)) {
                    // Get folder id where file/folder is located, it can be different than current folder id in trash.
                    var folder_id = Folders.getDataFolder(del);
                    if(folder_id === false)
                        return false;
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
				                    Folders.open(Folders.id);
				                }
				            }
				            xhr.send("ids="+folder_id+"&files="+id);
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
				                    Folders.open(Folders.id);
				                }
				            }
				            xhr.send("ids="+folder_id+"&folders="+id);
				        }
				    }
				}
			}
		},

		multiple : function() {
			var id = 0;
			var folderName;
            var folder_id;
            // Folder id tab where files/folders are located, it can be different than current folder id in trash.
            var filesFolderId = [];
            var foldersFolderId = [];

			if(Selection.Files.length > 0 || Selection.Folders.length > 0) {
				if(confirm(txt.User.questionrm)) {
				    var wait = 2;
				    if(Selection.Folders.length > 0) {

                        // Get folder id where folder is located for each folder
                        for(var i=0; i<Selection.Folders.length; i++) {
                            folder_id = Folders.getDataFolder("d"+Selection.Folders[i]);
                            if(folder_id === false)
                                return false;
                            foldersFolderId.push(folder_id);
                        }
                        if(Selection.Folders.length != foldersFolderId.length)
                            return false;

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
				        xhr.send("ids="+encodeURIComponent(foldersFolderId.join("|"))+"&folders="+encodeURIComponent(Selection.Folders.join("|")));
				    }
				    else
				        wait--;

				    if(Selection.Files.length > 0) {

                        // Get folder id where file is located for each file
                        for(var i=0; i<Selection.Files.length; i++) {
                            folder_id = Folders.getDataFolder("f"+Selection.Files[i]);
                            if(folder_id === false)
                                return false;
                            filesFolderId.push(folder_id);
                        }
                        if(Selection.Files.length != filesFolderId.length)
                            return false;

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
				        xhr2.send("ids="+encodeURIComponent(filesFolderId.join("|"))+"&files="+encodeURIComponent(Selection.Files.join("|")));
				    }
				    else
				        wait--;

				    var timer = setInterval(function() {
				        console.log("waiting...");
				        if(wait == 0) {
				            clearInterval(timer);
				            Folders.open(Folders.id);
				        }
				    }, 250);
				}
			}
		}
	}
});

var Favorites = (function() {
    return {
        update : function(fav) {
            Box.hide();
            if(fav.length > 1) {
                var id = fav.substr(1);
                if(isNumeric(id)) {
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "User/Favorites", true);
                    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

                    xhr.onreadystatechange = function()
                    {
                        if(xhr.status == 200 && xhr.readyState == 4)
                        {
                            //
                        }
                    }
                    xhr.send("id="+id);
                }
            }
        }
    }
});

var ExtIcons = (function() {
	// Private
	var ext = '';
	var pos = -1;
	var icon;

	// Types of files
	var archive = ['zip', 'tar', 'gz', 'bz', 'bz2', 'xz', 'rar', 'jar', '7z'];
	var code = ['php', 'html', 'htm', 'php3', 'php4', 'php5', 'java', 'css', 'scss', 'xml', 'svg', 'sql', 'c', 'cpp', 'cs', 'js', 'au3', 'asm', 'h', 'ini', 'jav', 'p', 'pl', 'rb', 'sh', 'bat', 'py'];
	var image = ['jpg', 'jpeg', 'png', 'bmp', 'gif'];
	var doc = ['docx', 'odt', 'doc', 'odp'];
	var pdf = ['pdf'];
	var sound = ['mp3', 'ogg', 'flac', 'wav', 'aac', 'm4a'];
	var video = ['mp4', 'avi', 'wmv', 'mpeg', 'mov', 'mkv', 'mka', 'mks', 'flv'];

	// Public
	return {
		set : function() {
			var dir_files = document.querySelectorAll(".file");
			for(var i = 0; i < dir_files.length; i++) {
				icon = 'text';
				pos = dir_files[i].getAttribute('data-title').lastIndexOf('.');
				if(pos !== -1) {
					ext = dir_files[i].getAttribute('data-title').substr(pos+1);
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
});

//                          //
//      End of modules      //
//                          //

//      App launcher

var UserLoader = function(folder_id) {
    console.log("Loading application.");

    // Get txt from user's language json (language.js)
    getJSON();

    /*
    */
    returnArea = document.querySelector("#returnArea");

    console.log(Request.modulesLoaded);
    if(Request.modulesLoaded === undefined || Request.modulesLoaded === false) {
        // Load modules
        console.log("Loading modules.");
        Box = Box();
        Arrows = Arrows();
        Selection = Selection();
        Move = Move();
        Upload = Upload();
    	Files = Files();
    	Folders = Folders();
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
                    event.preventDefault();
                    Folders.back();
                    break;
                case 46:
                    // suppr
                    Rm.multiple();
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
                    if(Selection.Files.length == 1 && Selection.Folders.length == 0)
                        Files.dl("f"+Selection.Files[0]);
                    else if(Selection.Files.length == 0 && Selection.Folders.length == 1)
                        Folders.open(Selection.Folders[0]);
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
