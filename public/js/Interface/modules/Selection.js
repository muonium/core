// Selection module. Loaded in window.onload()
var Selection = (function() {
    // Private
    var showDetails = (localStorage.getItem('details') == 'false') ? false : true;

    // Public
    // addSel : 1 => add a new selection
    // Files : Selected files (id)
    // Folders : Selected folders (id)
    return {
        addSel : 0,
        Files : [],
        Folders : [],
        multiple : false,

        select : function(id, putDetails = true) {
            if(document.querySelector("#"+id)) {
                document.querySelector("#"+id).style.backgroundColor='#E0F0FA';
                if(showDetails || (window.innerWidth || document.body.clientWidth) < 700) {
                    //document.querySelector("section#selection").className = 'selected';
                    Selection.openDetails();
                }
            }
            if(putDetails === true)  {
                if(showDetails || (window.innerWidth || document.body.clientWidth) < 700) {
                    Selection.putDetails(id);
                }
                Toolbar.display(id);
            }
        },

        unselect : function(id, putDetails = true) {
            if(document.querySelector("#"+id)) {
                document.querySelector("#"+id).style.backgroundColor='white';
                //document.querySelector("section#selection").className = '';
            }
            if(putDetails === true)  {
                Toolbar.display();
            }
        },

        add : function(id, m = null) {
            if(id.length > 1) {
                if(id.substr(0, 1) == 'd')
                    Selection.addFolder(m, id);
                else if(id.substr(0, 1) == 'f')
                    Selection.addFile(m, id);
            }
        },

        addFile : function(event, id, putDetails = true) {
            Selection.addSel = 1;
            if(document.querySelector("#"+id)) {
                if(Selection.multiple || (event !== null && (event == 'ctrl' || event.ctrlKey))) {
                    var pos = Selection.Files.indexOf(id.substr(1));
                    if(pos != -1) {
                        Selection.Files.splice(pos, 1);
                        Selection.unselect(id, putDetails);
                    }
                    else {
                        Selection.Files.push(id.substr(1));
                        Selection.select(id, putDetails);
                    }
                }
                else {
                    Selection.remove();
                    Selection.Files.push(id.substr(1));
                    Selection.select(id, putDetails);
                }
            }
        },

        addFolder : function(event, id, putDetails = true) {
            Selection.addSel = 1;
            if(document.querySelector("#"+id)) {
                if(Selection.multiple || (event !== null && (event == 'ctrl' || event.ctrlKey))) {
                    var pos = Selection.Folders.indexOf(id.substr(1));
                    if(pos != -1) {
                        Selection.Folders.splice(pos, 1);
                        Selection.unselect(id, putDetails);
                    }
                    else {
                        Selection.Folders.push(id.substr(1));
                        Selection.select(id, putDetails);
                    }
                }
                else {
                    Selection.remove();
                    Selection.Folders.push(id.substr(1));
                    Selection.select(id, putDetails);
                }
            }
        },

        invert : function() {
            Selection.addSel = 1; //
            var i = 0;
            var files = document.querySelectorAll(".file");
            for(i=0;i<files.length;i++)
                Selection.addFile('ctrl', files[i].id, false);

            var folders = document.querySelectorAll(".folder");
            for(i=0;i<folders.length;i++)
                Selection.addFolder('ctrl', folders[i].id, false);
        },

        all : function() {
            Selection.addSel = 1;
            var i = 0;
            var files = document.querySelectorAll(".file");
            for(i=0;i<files.length;i++) {
                if(document.querySelector("#"+files[i].id)) {
                    if(Selection.Files.indexOf((files[i].id).substr(1)) == -1) {
                        Selection.Files.push((files[i].id).substr(1));
                        Selection.select(files[i].id, false);
                    }
                }
            }

            var folders = document.querySelectorAll(".folder");
            for(i=0;i<folders.length;i++) {
                if(document.querySelector("#"+folders[i].id)) {
                    if(Selection.Folders.indexOf(folders[i].id.substr(1)) == -1) {
                        Selection.Folders.push(folders[i].id.substr(1));
                        Selection.select(folders[i].id, false);
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
        },

        openDetails : function() {
            if(!($("section#selection").hasClass("selected"))) {
                $("section#selection").fadeIn(400, function() {
                    $(this).addClass("selected");
                });
            }
        },

        closeDetails : function() {
            if($("section#selection").hasClass('selected')) {
                $("section#selection").fadeOut('fast', function() {
                    $(this).removeClass("selected");
                });
            }
        },

        dl : function(id) {
            if(Selection.Files.length > 0) {
                var sel = Selection.Files;
                var i = 0;
                var timer = setInterval(function() {
                    Files.dl("f"+sel[i]);
                    i++;
                    if(i >= sel.length)
                        clearInterval(timer);
                }, 1000);
            }
            else if(id !== undefined) {
                Files.dl(id);
            }
        },

        multipleSwitch : function(el) {
            if(document.querySelector("#"+el).checked) {
                Selection.multiple = true;
            }
            else {
                Selection.multiple = false;
            }
        },

        allSwitch : function() {
            Selection.all();
            if(Selection.Files.length > 0) {
                Toolbar.display('f'+Selection.Files[0]);
            }
            else if(Selection.Folders.length > 0) {
                Toolbar.display('d'+Selection.Folders[0]);
            }
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
                if(title[1] !== undefined) content += '<span class="btn_download" onclick="Selection.dl(\''+id+'\')"><i class="fa fa-download" aria-hidden="true"></i> '+txt.RightClick.dl+'</span>';
                if(Selection.Folders.length + Selection.Files.length > 1) {
                    content += "<hr><span class='multiselected_details'>"+Selection.Folders.length+" "+txt.User.folderSelected+", "+Selection.Files.length+" "+txt.User.fileSelected+"</span>";
                }
                document.querySelector("section#selection").innerHTML = content + "</ul>";
            }
        }
    }
});
