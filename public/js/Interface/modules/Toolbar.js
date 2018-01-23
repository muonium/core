// Toolbar module. Loaded in window.onload()
var Toolbar = (function() {
    // Private

    // Public
    return {
        Area : 0,
        display: function(id) {
            if(id === undefined) {
                Toolbar.Area = 0;
            } else if(id.length > 1) {
                if(id.substr(0, 1) == 'f') Toolbar.Area = 1;
                if(id.substr(0, 1) == 'd') Toolbar.Area = 2;
            }

            var div = document.querySelector("#toolbar");
            var data = '<ul>';
            switch(Toolbar.Area) {
                case 1:
                    data += '<li onclick="Selection.dl(\''+id+'\')"><i class="fa fa-download" aria-hidden="true"></i></li>';
					if(Files.isShared(id.substr(1))) {
						data += '<li onclick="Selection.unshare(\''+id.substr(1)+'\')"><i class="fa fa-ban" aria-hidden="true"></i></li>';
					} else {
						data += '<li onclick="Selection.share(\''+id.substr(1)+'\')"><i class="fa fa-share" aria-hidden="true"></i></li>';
					}
                    if(Trash.state == 0) {
                        //data += '<li onclick="Favorites.update(\''+id+'\')"><i class="fa fa-star" aria-hidden="true"></i></li>';
                        data += '<li onclick="Move.cut(\''+id+'\')"><i class="fa fa-scissors" aria-hidden="true"></i></li>';
                        data += '<li onclick="Move.copy(\''+id+'\')"><i class="fa fa-clone" aria-hidden="true"></i></li>';
                        data += '<li onclick="Move.trashMultiple(\''+id+'\')"><i class="fa fa-trash" aria-hidden="true"></i></li>';
                    } else { data += '<li onclick="Move.trashMultiple(\''+id+'\')"><i class="fa fa-undo" aria-hidden="true"></i></li><li onclick="Rm.multiple(\''+id+'\')"><i class="fa fa-trash" aria-hidden="true"></i></li>'; }
                    if(Trash.state == 0) {
                        data += '<li onclick="Move.rename(\''+id+'\')"><i class="fa fa-pencil" aria-hidden="true"></i></li>';
					}
                    break;
                case 2:
                    data += '<li onclick="Folders.open(\''+id.substr(1)+'\')"><i class="fa fa-folder-open" aria-hidden="true"></i></li>';
                    if(Trash.state == 0) {
                        data += '<li onclick="Move.cut(\''+id+'\')"><i class="fa fa-scissors" aria-hidden="true"></i></li>';
                        data += '<li onclick="Move.copy(\''+id+'\')"><i class="fa fa-clone" aria-hidden="true"></i></li>';
                        data += '<li onclick="Move.trashMultiple(\''+id+'\')"><i class="fa fa-trash" aria-hidden="true"></i></li>';
                    } else { data += '<li onclick="Move.trashMultiple(\''+id+'\')"><i class="fa fa-undo" aria-hidden="true"></i></li><li onclick="Rm.multiple(\''+id+'\')"><i class="fa fa-trash" aria-hidden="true"></i></li>'; }
                    if(Trash.state == 0) {
                        data += '<li onclick="Move.rename(\''+id+'\')"><i class="fa fa-pencil" aria-hidden="true"></i></li>';
					}
                    break;
                default:
                    data += '<li class="select"><input type="checkbox" id="multisel_toolbar" onclick="Selection.multipleSwitch(this.id)"';
                    if(Selection.multiple) data += ' checked';
                    data += '/> <label for="multisel_toolbar">'+txt.UserMenu.multiSelection+'</label><br>';
                    data += '<input type="checkbox" id="selectall_toolbar" onchange="Selection.allSwitch()"> <label for="selectall_toolbar">'+txt.UserMenu.selectAll+'</label></li>';
                    if(Trash.State == 0) {
                        data += '<li onclick="Folders.create()"><i class="fa fa-folder-o" aria-hidden="true"></i></li>';
                        data += '<li onclick="Upload.dialog()"><i class="fa fa-upload" aria-hidden="true"></i></li>';
                        if(Move.Files.length > 0 || Move.Folders.length > 0) {
                            data += '<li onclick="Move.paste(\''+id+'\')"><i class="fa fa-clipboard" aria-hidden="true"></i></li>';
                    	}
                    }
            }
            data += '</ul>';

            div.innerHTML = data;
        }
    }
});
