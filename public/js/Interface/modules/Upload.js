// Upload module. Loaded in window.onload()
var Upload = (function() {
    // Private
	var f_enc = [];
	var f_files;

    // Public
    return {
        dialog : function() {
            document.querySelector('#upFilesInput').click();
        },

        abort : function() {
			var i = this.getAttribute('data-id');
			console.log("Aborting "+i);
			f_enc[i].abort();
        },

		read : function(i) {
			console.log("read "+i);
			f_enc[i].read();
		},

		upFile : function(file_id) {
			var up, btn, spn;
			console.log("uploading file "+file_id+"/"+(f_files.length-1));
			up = document.createElement('div');
			up.id = 'div_upload'+file_id;

			btn = document.createElement('button');
			btn.setAttribute('data-id', file_id);
			btn.onclick = Upload.abort;
			btn.innerHTML = '- X -';

			spn = document.createElement('span');
			spn.id = 'span_upload'+file_id;

			up.appendChild(btn);
			up.appendChild(spn);
			document.querySelector("#progress").appendChild(up);

			if(file_id == f_files.length-1) {
				console.log("stop");
				f_enc[file_id] = new Encryption(f_files[file_id], Folders.id, file_id, null);
			}
			else {
				console.log("next");
				f_enc[file_id] = new Encryption(f_files[file_id], Folders.id, file_id, function() {
					Upload.upFile(file_id+1);
				});
			}
		},

        upFiles : function(files) {
			f_files = files;
            document.querySelector("#progress").innerHTML = ' ';
			Upload.yesReplaceAll = false;
			Upload.yesCompleteAll = false;
			Upload.noAll = false;
            Upload.upFile(0);
        },

		yesReplaceAll: false,
		yesCompleteAll: false,
		noAll: false
    }
});
