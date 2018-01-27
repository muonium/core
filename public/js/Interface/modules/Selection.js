// Selection module. Loaded in window.onload()
var Selection = (function() {
    // addSel : 1 => add a new selection
    // Files : Selected files (id)
    // Folders : Selected folders (id)
    return {
        addSel : 0,
        Files : [],
        Folders : [],
        multiple : false,

        select : function(id) {
            if($('#'+id).length) {
                $('#'+id).addClass('selected').find('#sel_'+id).prop('checked', true);
            }
            Selection.putDetails(id);
            //Toolbar.display(id);
        },

        unselect : function(id) {
            if($('#'+id).length) {
				$('#'+id).removeClass('selected').find('#sel_'+id).prop('checked', false);
            }
			setTimeout(function() {
				$('#sel_all').prop('checked', false);
			}, 0);
            //Toolbar.display();
        },

        add : function(id, m = null) {
            if(id.length > 1) {
                if(id.substr(0, 1) == 'd') {
                    Selection.addFolder(m, id);
				} else if(id.substr(0, 1) == 'f') {
                    Selection.addFile(m, id);
				}
            }
        },

        addFile : function(event, id) {
			if(typeof event === 'object' && event !== null) event.preventDefault(); // Prevent event to be fired twice in some cases (due to input checkbox and label)
            Selection.addSel = 1;
            if(document.querySelector("#"+id)) {
                if(Selection.multiple || (event !== null && (event == 'ctrl' || event.ctrlKey))) {
                    var pos = Selection.Files.indexOf(id.substr(1));
                    if(pos != -1) {
                        Selection.Files.splice(pos, 1);
                        Selection.unselect(id);
                    } else {
                        Selection.Files.push(id.substr(1));
                        Selection.select(id);
                    }
                }
                else {
                    Selection.remove();
                    Selection.Files.push(id.substr(1));
                    Selection.select(id);
                }
            }
        },

        addFolder : function(event, id) {
			if(typeof event === 'object' && event !== null) event.preventDefault(); // Prevent event to be fired twice in some cases (due to input checkbox and label)
            Selection.addSel = 1;
            if(document.querySelector("#"+id)) {
                if(Selection.multiple || (event !== null && (event == 'ctrl' || event.ctrlKey))) {
                    var pos = Selection.Folders.indexOf(id.substr(1));
                    if(pos != -1) {
                        Selection.Folders.splice(pos, 1);
                        Selection.unselect(id);
                    } else {
                        Selection.Folders.push(id.substr(1));
                        Selection.select(id);
                    }
                }
                else {
                    Selection.remove();
                    Selection.Folders.push(id.substr(1));
                    Selection.select(id);
                }
            }
        },

        invert : function() {
            Selection.addSel = 1;
            var files = document.querySelectorAll(".file");
            for(var i = 0; i < files.length; i++) {
                Selection.addFile('ctrl', files[i].id);
			}

            var folders = document.querySelectorAll(".folder");
            for(var i = 0; i < folders.length; i++) {
                Selection.addFolder('ctrl', folders[i].id);
			}
        },

        all : function() {
            Selection.addSel = 1;
            var files = document.querySelectorAll(".file");
            for(var i = 0; i < files.length; i++) {
                if(document.querySelector("#"+files[i].id)) {
                    if(Selection.Files.indexOf((files[i].id).substr(1)) == -1) {
                        Selection.Files.push((files[i].id).substr(1));
                        Selection.select(files[i].id);
                    }
                }
            }

            var folders = document.querySelectorAll(".folder");
            for(var i = 0; i < folders.length; i++) {
                if(document.querySelector("#"+folders[i].id)) {
                    if(Selection.Folders.indexOf(folders[i].id.substr(1)) == -1) {
                        Selection.Folders.push(folders[i].id.substr(1));
                        Selection.select(folders[i].id);
                    }
                }
            }
			setTimeout(function() {
				$('#sel_all').prop('checked', true);
			}, 0);
        },

        remove : function() {
            for(var i = 0; i < Selection.Files.length; i++) {
                Selection.unselect("f"+Selection.Files[i]);
			}
            for(var i = 0; i < Selection.Folders.length; i++) {
                Selection.unselect("d"+Selection.Folders[i]);
			}
            Selection.Files = [];
            Selection.Folders = [];
        },

        dl : function(id) {
            if(Selection.Files.length > 0) {
                var sel = Selection.Files;
                var i = 0;
                var timer = setInterval(function() {
                    Files.dl("f"+sel[i]);
                    i++;
                    if(i >= sel.length) clearInterval(timer);
                }, 1000);
            }
            else if(id !== undefined) {
                Files.dl(id);
            }
        },

		share : function(id) {
			Box.hide();
			var validate = function() {
				var passphrase = this.$inputs.passphrase.value;
				if(typeof(passphrase) !== 'string') return false;
				if(Selection.Files.length > 0) {
					for(var i = 0; i < Selection.Files.length; i++) {
		                Files.share(Selection.Files[i], passphrase);
					}
				}
				else if(id !== undefined) {
					Files.share(id, passphrase);
				}
			};

			var m = new MessageBox(txt.Register.passphrase).addInput('passphrase', {
				id: "nShare",
				placeholder: txt.Register.passphrase,
				autocomplete: "off",
				oninput: function(event) {
					if(this.$inputs.passphrase.value.length >= 6) {
						$(this.$elemBtns).find('input,button').prop('disabled', false);
						if(typeof(event) !== 'undefined' && event.keyCode == 13) {
							validate.bind(this)();
							return this.close();
						}
					} else {
						$(this.$elemBtns).find('input,button').prop('disabled', true);
					}
					return true;
				}
			}, 'fa fa-lock').addButton(txt.User.generatelink, validate).show();
			$(m.$elemBtns).find('input,button').prop('disabled', true);
		},

		unshare : function(id) {
			Box.hide();
			if(Selection.Files.length > 0) {
				for(var i = 0; i < Selection.Files.length; i++) {
	                Files.unshare(Selection.Files[i]);
				}
			}
			else if(id !== undefined) {
				Files.unshare(id);
			}
		},

        multipleSwitch : function(el) {
			Selection.multiple = false;
            if(document.querySelector("#"+el).checked) {
                Selection.multiple = true;
            }
        },

        allSwitch : function() {},

		removeDetails: function() {
			$('section.selection').html('');
		},

        putDetails: function(id) {
            if(elem = document.querySelector("#"+id)) {
    			var title = elem.getAttribute("title").split("\n");
                var content = "<strong>"+txt.User.details+"</strong>\
            	<hr><ul><li><strong>"+txt.User.name+"</strong> : "+elem.getAttribute("data-title")+"</li>\
            	<li><strong>"+txt.User.path+"</strong> : "+ (elem.getAttribute("data-path") == '' ? "/" : elem.getAttribute("data-path")) +"</li>\
                <li><strong>"+txt.User.type+"</strong> : "+ (title[1] ? txt.User.file : txt.User.folder) +"</li>\
                <li><strong>"+txt.User.size+"</strong> : "+title[0]+"</li>";

                if(title[1] !== undefined) content += "<li>"+title[1]+"</li>"; // File
                content += '</ul>';
                if(title[1] !== undefined) {
					content += '<span class="btn_download" onclick="Selection.dl(\''+id+'\')"><i class="fa fa-download" aria-hidden="true"></i> '+txt.RightClick.dl+'</span>';
					if(Files.isShared(id.substr(1))) {
						content += '<span class="btn_share" onclick="Selection.unshare(\''+id.substr(1)+'\')"><i class="fa fa-ban" aria-hidden="true"></i> '+txt.RightClick.unshare+'</span>';
						content += '<input type="text" value="'+elem.getAttribute("data-url")+'" class="copy_url">';
						content += '<input type="button" value="'+txt.RightClick.copy+'" onclick="copy_url()">';
					} else {
						content += '<span class="btn_share" onclick="Selection.share(\''+id.substr(1)+'\')"><i class="fa fa-share" aria-hidden="true"></i> '+txt.RightClick.share+'</span>';
					}
				}
				if(Selection.Folders.length + Selection.Files.length > 1) {
                    content += "<hr><span class='multiselected_details'>"+Selection.Folders.length+" "+txt.User.folderSelected+", "+Selection.Files.length+" "+txt.User.fileSelected+"</span>";
                }
                document.querySelector("section.selection").innerHTML = content + "</ul>";
            }
        }
    }
});
