// Folders module. Loaded in window.onload()
var Folders = (function() {
	// Private

	// Public
	return {
		id : 0,
		create : function() {
			Box.hide();

			var validate = function() {
				var folder_name = this.$inputs.folder_name.value;
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
				            var content = '<img src="'+IMG+'desktop/extensions/folder.svg" class="icon"> <strong>'+folder_name+'</strong> [0 '+txt.User.element+']';

							var span = document.createElement('span');
							span.className = 'folder';
							span.id = 'd'+xhr.response;
							span.name = folder_name;
							span.title = '0';
							span.dataset.folder = Folders.id;
							span.dataset.path = '?';
							span.dataset.title = folder_name;
							span.onclick = function(){ Selection.addFolder(event, this.id); };
							span.ondblclick = function(){ Folders.open(this.id.substr(1)); };
							span.innerHTML = content;

							// Check if there is already file or folder
				            if(!(first = document.querySelector(".folder"))) {
				                if(!(first = document.querySelector(".file"))) {
									document.querySelector("#tree").appendChild(span);
								}
							}

							if(first !== null && first !== undefined) {
				                first.parentNode.insertBefore(span, first);
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
				xhr.send("folder_id="+Folders.id+"&folder="+encodeURIComponent(this.$inputs.folder_name.value));
			};

			var m = new MessageBox(txt.User.foldername).addInput('folder_name', {
				id: "nFolder",
				autocomplete: "off",
				onkeypress: function(event) {
					if(event.keyCode == 13) {
						validate.bind(this)();
						return this.close();
					}
					return Folders.verif(event);
				},
				autofocus: "autofocus"
			}).addButton("OK", validate).show();
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

						Files.display(Files.style);
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
                <i class='fa fa-chevron-left' aria-hidden='true' onclick=\"Box.right_click(event.clientX, event.clientY, '"+el+"')\"></i> &nbsp;&nbsp;<strong>"+txt.User.details+"</strong>\
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
