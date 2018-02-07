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
				var animate = Folders.id == folder_id ? false : true;
				Folders.id = parseInt(folder_id);
				$('#mui').hide().html(data);
				// Later, use specific API method to get quota and stored
				var quota = $('#mui').find('.quota');
				$(quota).remove();
				$('#quota_container').html($(quota).html());
				$('#tree').show();

				if($('#mui').find('tr:not(#tree_head):not(.break)').length === 0 && Folders.id === 0) {
					// Nothing stored at root folder
					$('#tree').hide();
					if(Trash.state === 0) {
						$('#mui').append('<div class="info mtop"><a onclick="showHelp()">'+txt.User.needhelp+'</a></div>\
							<div class="bloc-nothing" onclick="Upload.dialog()">'+txt.User.nothing+'<br><img src="'+ROOT+'public/pictures/desktop/ic-no-uploads.png"><br><span>'+txt.User.first+'</span></div>\
						');
					} else {
						$('#mui').append('<div class="info mtop"><a onclick="showHelp()">'+txt.User.needhelp+'</a></div>\
							<div class="bloc-trash-nothing">'+txt.User.Trashnothing+'<br><img src="'+ROOT+'public/pictures/desktop/ic-no-trash-'+THEME+'.png"></div>\
						');
					}
				}

				if(animate) {
					$('#mui').fadeIn(400);
				} else {
					$('#mui').show();
				}
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
            var elem = $('#'+el);
            if($(elem).length) {
                Box.box_more = true;
                Box.reset();
                Box.Area = 2;
                Box.set('<div class="close" onclick="Box.close()">x</div>\
				<div class="details">\
                	<strong>'+txt.User.details+'</strong>\
                	<ul>\
						<li><span class="label">'+txt.User.name+':</span> '+$(elem).attr("name")+'</li>\
                		<li><span class="label">'+txt.User.path+':</span> '+$(elem).attr("data-path")+'/</li>\
                		<li><span class="label">'+txt.User.type+':</span> '+txt.User.folder+'</li>\
                		<li><span class="label">'+txt.User.size+':</span> '+$(elem).attr("title")+'</li>\
                	</ul>\
				</div>');
                Box.show();
            }
        }
	}
});
