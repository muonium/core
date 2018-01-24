// Folders module. Loaded in window.onload()
var Folders = (function() {
	return {
		id : 0,
		create : function(e) {
			if(e !== undefined) e.preventDefault();
			Box.hide();

			var validate = function() {
				var folder_name = this.$inputs.folder_name.value;
				$.post('User/AddFolder', {folder_id: Folders.id, folder: encodeURIComponent(this.$inputs.folder_name.value)}, function(data) {
					if(isNumeric(data)) {
						Folders.open(Folders.id);
					}
				});
			};

			var m = new MessageBox(txt.RightClick.nFolder).addInput('folder_name', {
				id: "nFolder",
				placeholder: txt.User.foldername,
				autocomplete: "off",
				onkeypress: function(event) {
					if(event.keyCode == 13) {
						validate.bind(this)();
						return this.close();
					}
					return Folders.verif(event);
				}
			}, 'fa fa-folder-o').addButton("OK", validate).show();
		},

		open : function(folder_id) {
			if(!isNumeric(folder_id)) return false;
			$.post('User/ChangePath', {folder_id: folder_id, trash: Trash.state}, function(data) {
				$('#mui').hide().html(data);
				// Later, use specific API method to get quota and stored
				var quota = $('#mui').find('.quota');
				$(quota).remove();
				$('#quota_container').html($(quota).html());
				$('#tree').show();

				if($('#mui').find('tr:not(#tree_head)').length === 0 && parseInt($(quota).find('strong').first().html()) === 0) {
					// Nothing stored, not only in this folder
					$('#tree').hide();
					$('#mui').append('<div class="info mtop"><a onclick="showHelp()">'+txt.User.needhelp+'</a></div>\
						<div class="bloc-nothing" onclick="Upload.dialog()">'+txt.User.nothing+'<br><img src="'+ROOT+'public/pictures/desktop/ic-no-uploads.png"><br><span>'+txt.User.first+'</span></div>\
					');
				}

				$('#mui').show();
				Folders.id = parseInt(folder_id);
				Box.hide();

				// reset selected files/folders
				Selection.Files = [];
				Selection.Folders = [];
				// Set events for all files and folders loaded
				setEvents();
				// Put icons
				ExtIcons.set();

				Files.display(Files.style);
			});
		},

        back : function() {
            // Open parent folder
            var parent = document.querySelector("a[id^=parent-]");
            if(parent) {
                Folders.open(parent.id.substr(parent.id.lastIndexOf("-")+1));
			}
        },

		verif : function(evt) {
			var keyCode = evt.which ? evt.which : evt.keyCode;
			if(keyCode == 13) { // Submit
				Folders.create();
				return false;
			}
			var interdit = '/\\:*?<>|"';
			if(interdit.indexOf(String.fromCharCode(keyCode)) >= 0) {
				return false;
			}
			return true;
		},

		getName : function(folder_id) {
			if(document.getElementById(folder_id)) {
				return document.getElementById(folder_id).getAttribute("name");
			}
			return false;
		},

        getDataFolder : function(elem_id) {
            if(document.getElementById(elem_id)) {
                return document.getElementById(elem_id).getAttribute("data-folder");
			}
            return false;
        },

        details : function(el) {
            var elem;
            if(elem = document.querySelector("#"+el)) {
                Box.box_more = true;
                Box.reset();
                Box.Area = 2;

                Box.set("<div>\
                <p onclick=\"Box.right_click(event.clientX, event.clientY, '"+el+"')\"><i class='fa fa-chevron-left' aria-hidden='true'></i> &nbsp;&nbsp;<strong>"+txt.User.details+"</strong></p>\
                <hr><ul><li>"+txt.User.name+" : "+elem.getAttribute("name")+"</li>\
                <li>"+txt.User.path+" : "+elem.getAttribute("data-path")+"/</li>\
                <li>"+txt.User.type+" : "+txt.User.folder+" <span class='ext_icon'></span></li>\
                <li>"+txt.User.size+" : "+elem.innerHTML.substr(elem.innerHTML.lastIndexOf("["))+"</li>\
                </ul></div>");

                var newNode = document.importNode(elem.getElementsByTagName('img')[0], true);
                document.querySelector(".ext_icon").appendChild(newNode);
                Box.show();
            }
        }
	}
});
