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
