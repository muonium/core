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
