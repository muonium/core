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
        selected : '',

        init : function() {
            box_div = $("#box")[0];
            init = true;
        },

        hide : function() {
            if(!init) return false;
            $(box_div).fadeOut(200);
        },

        show : function() {
            if(!init) return false;
            $(box_div).fadeIn(300);
        },

        reset : function() {
            if(!init) return false;
            $(box_div).html(' ');
        },

        set : function(content) {
            if(!init) return false;
            $(box_div).html(content);
        },

        left_click : function(cx, cy) {
            if(!init) return false;
            // If the user uses left click inside the 'box'
            if((cx >= x && cx <= (x + box_div.clientWidth)) && (cy >= y && cy <= (y + box_div.clientHeight)) || Box.box_more) {
                // Action
                Box.box_more = false;
            }
            else { // Otherwise, hide 'box'
                if(Box.selected != '') Selection.unselect(Box.selected);
                Box.selected = '';
                Box.hide();
                Box.Area = 0;
            }
        },

        right_click : function(cx, cy, id) {
            if(!init) return false;
            if(Box.selected != '') Selection.unselect(Box.selected);
            Box.selected = '';
            // Show box at position x, y
            x = cx;
            y = cy;

            if(id === undefined) { //when there isn't anything under the mouse
                Box.Area = 0;
			}

            // Content according to area
            switch(Box.Area) {
                //over nothing
                case 0:
                    if(Trash.state == 0) {
                        box_div.innerHTML = '<p onclick="Folders.create()"><img src="'+IMG+'desktop/actions/create_folder.svg" class="icon"> '+txt.RightClick.nFolder+'</p>';
                        box_div.innerHTML += '<p onclick="Upload.dialog()"><i class="fa fa-upload" aria-hidden="true"></i> '+txt.RightClick.upFiles+'</p>';
                        if(Move.Files.length > 0 || Move.Folders.length > 0) {
                            box_div.innerHTML += '<hr><p onclick="Move.paste(\''+id+'\')"><i class="fa fa-clipboard" aria-hidden="true"></i> '+txt.RightClick.paste+'</p>';
						}
                    }
                    break;
                //mouse over a file
                case 1:
                    Box.selected = id;
                    Selection.select(id);
                    box_div.innerHTML = '<p onclick="Selection.dl(\''+id+'\')"><i class="fa fa-download" aria-hidden="true"></i> '+txt.RightClick.dl+'</p>';
					if(Files.isShared(id.substr(1))) {
						box_div.innerHTML += '<p onclick="Selection.unshare(\''+id.substr(1)+'\')"><i class="fa fa-ban" aria-hidden="true"></i> '+txt.RightClick.unshare+'</p><hr>';
					} else {
						box_div.innerHTML += '<p onclick="Selection.share(\''+id.substr(1)+'\')"><i class="fa fa-share" aria-hidden="true"></i> '+txt.RightClick.share+'</p><hr>';
					}
					if(Trash.state == 0) {
                        //box_div.innerHTML += '<p onclick="Favorites.update(\''+id+'\')"><i class="fa fa-star" aria-hidden="true"></i> '+txt.RightClick.star+'</p><hr>';
                        box_div.innerHTML += '<p onclick="Move.cut(\''+id+'\')"><i class="fa fa-scissors" aria-hidden="true"></i> '+txt.RightClick.cut+'</p>';
                        box_div.innerHTML += '<p onclick="Move.copy(\''+id+'\')"><i class="fa fa-clone" aria-hidden="true"></i> '+txt.RightClick.copy+'</p>';
                        box_div.innerHTML += '<p onclick="Move.trashMultiple(\''+id+'\')"><i class="fa fa-trash" aria-hidden="true"></i> '+txt.RightClick.trash+'</p>';
                    } else {
                        box_div.innerHTML += '<p onclick="Move.trashMultiple(\''+id+'\')"><i class="fa fa-undo" aria-hidden="true"></i> '+txt.RightClick.restore+'</p>';
                        box_div.innerHTML += '<p onclick="Rm.multiple(\''+id+'\')"><i class="fa fa-trash" aria-hidden="true"></i> '+txt.RightClick.rm+'</p>';
                    }
                    if(Trash.state == 0) {
                        box_div.innerHTML += '<hr><p onclick="Move.rename(\''+id+'\')"><i class="fa fa-pencil" aria-hidden="true"></i> '+txt.RightClick.mvItem+'</p>';
                    }
                    box_div.innerHTML += '<hr><p onclick="Files.details(\''+id+'\')"><i class="fa fa-info" aria-hidden="true"></i> '+txt.RightClick.vDetails+'</p>';
                    break;
                //mouse over a folder
                case 2:
                    Box.selected = id;
                    Selection.select(id);
                    box_div.innerHTML = '<p onclick="Folders.open(\''+id.substr(1)+'\')"><i class="fa fa-folder-open" aria-hidden="true"></i> '+txt.RightClick.open+'</p><hr>';
                    if(Trash.state == 0) {
                        box_div.innerHTML += '<p onclick="Move.cut(\''+id+'\')"><i class="fa fa-scissors" aria-hidden="true"></i> '+txt.RightClick.cut+'</p>';
                        box_div.innerHTML += '<p onclick="Move.copy(\''+id+'\')"><i class="fa fa-clone" aria-hidden="true"></i> '+txt.RightClick.copy+'</p>';
                        box_div.innerHTML += '<p onclick="Move.trashMultiple(\''+id+'\')"><i class="fa fa-trash" aria-hidden="true"></i> '+txt.RightClick.trash+'</p>';
                    } else {
                        box_div.innerHTML += '<p onclick="Move.trashMultiple(\''+id+'\')"><i class="fa fa-undo" aria-hidden="true"></i> '+txt.RightClick.restore+'</p>';
                        box_div.innerHTML += '<p onclick="Rm.multiple(\''+id+'\')"><i class="fa fa-trash" aria-hidden="true"></i> '+txt.RightClick.rm+'</p>';
                    }
                    if(Trash.state == 0) {
                        box_div.innerHTML += '<hr><p onclick="Move.rename(\''+id+'\')"><i class="fa fa-pencil" aria-hidden="true"></i> '+txt.RightClick.mvItem+'</p>';
                    }
                    box_div.innerHTML += '<hr><p onclick="Folders.details(\''+id+'\')"><i class="fa fa-info" aria-hidden="true"></i> '+txt.RightClick.vDetails+'</p>';
            }

            Box.show();

            if(x < 2) x = 2;
            if(x + box_div.clientWidth > document.body.clientWidth) x = document.body.clientWidth - box_div.clientWidth - 2;
            if(y < 5) y = 5;
            if(y + box_div.clientHeight > document.body.clientHeight) y = document.body.clientHeight - box_div.clientHeight - 5;

            box_div.style.left = x+'px';
            box_div.style.top = y+'px';
        }
    }
});
