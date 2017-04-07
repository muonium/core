var Rm = (function() {
	// Private

	// Public
	return {
		rm : function(del, callback = false, showConfirm = true) {
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
				        if(showConfirm === false || confirm(txt.User.questionf)) {
							document.querySelector("section#selection").className = '';
				            xhr.open("POST", "User/RmFiles", true);
				            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

				            xhr.onreadystatechange = function()
				            {
				                if(xhr.status == 200 && xhr.readyState == 4)
				                {
				                    console.log(xhr.responseText);
									if(callback !== false) {
										callback();
									}
									else {
				                    	Folders.open(Folders.id);
									}
				                }
				            }
				            xhr.send("ids="+folder_id+"&files="+id);
				        }
				    }
				    else if(del.substr(0, 1) == 'd') {
				        // folder
				        if(showConfirm === false || confirm(txt.User.questiond)) {
							document.querySelector("section#selection").className = '';
				            xhr.open("POST", "User/RmFolders", true);
				            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

				            xhr.onreadystatechange = function()
				            {
				                if(xhr.status == 200 && xhr.readyState == 4)
				                {
				                    console.log(xhr.responseText);
									if(callback !== false) {
										callback();
									}
									else {
				                    	Folders.open(Folders.id);
									}
				                }
				            }
				            xhr.send("ids="+folder_id+"&folders="+id);
				        }
				    }
				}
			}
		},

		multiple : function(rm_id) {
			var id = 0;
			var folderName;
            var folder_id;
            // Folder id tab where files/folders are located, it can be different than current folder id in trash.
            var filesFolderId = [];
            var foldersFolderId = [];

			if(Selection.Files.length > 0 || Selection.Folders.length > 0) {
				if(confirm(txt.User.questionrm)) {
					document.querySelector("section#selection").className = '';
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
			else if(rm_id !== undefined) {
				Rm.rm(rm_id);
			}
		}
	}
});
