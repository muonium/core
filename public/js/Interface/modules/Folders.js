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
				                        window.location.href=root+"User";
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
