// Upload module. Loaded in window.onload()
var Upload = (function() {
    // Private
    //var xhr_upload = new Array();
    var filesUploaded = 0; // Number of files uploaded

    // Public
    return {
        dialog : function() {
            document.querySelector('#upFilesInput').click();
        },

        abort : function(i) {
            document.querySelector("#div_upload"+i).style.display = 'none';
            /*xhr_upload[i].abort();
            console.log("aborted "+i);
            filesUploaded++;*/
        },

        upFiles : function(files) {
            // To change ?
            document.querySelector("#progress").innerHTML = ' ';
			//var up = [];
            // Loop through each of the selected files.
            for(var i=0;i<files.length;i++) {
                //document.querySelector("#progress").innerHTML += '<div id="div_upload'+i+'"><button onclick="Upload.abort('+i+')">X</button> <span id="span_upload'+i+'"></span></div>';
				//up.push(new Encryption(files[i], Folders.id));
				new Encryption(files[i], Folders.id);
            }

            // Waiting end of the uploading process
            /*var timer = setInterval(function() {
                console.log("waiting...");
                if(filesUploaded >= files.length) {
                    document.querySelector("#progress").innerHTML = ' ';
                    clearInterval(timer);
                    Folders.open(Folders.id);
                }
            }, 1000);*/
        },

        /*upFile : function(file, i) {
            // Upload a file
            // Create a new FormData object.
            var formData = new FormData();

            // Add the file to the request.
            formData.append('folder_id', Folders.id);
            formData.append('upload[]', file, file.name);
            xhr_upload[i] = new XMLHttpRequest();
            xhr_upload[i].open("POST", "User/UpFiles", true);

            // Progress bar
            xhr_upload[i].upload.addEventListener("progress", function(event, filename) {
                if(event.lengthComputable)
                    if(document.querySelector("#span_upload"+i))
                        document.querySelector("#span_upload"+i).innerHTML = file.name+" : "+(event.loaded/event.total*100).toFixed(2)+"%";
            }, false);

            xhr_upload[i].onreadystatechange = function() {
                if(xhr_upload[i].readyState === 4) {
                    if(xhr_upload[i].status === 200) {
                        console.log(xhr_upload[i].responseText);
                        filesUploaded++;
                    }
                }
            };
            xhr_upload[i].send(formData);
        }*/
    }
});
