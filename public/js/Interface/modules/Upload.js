// Upload module. Loaded in window.onload()
var Upload = (function() {
    // Private

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
			var up, btn, spn;
            document.querySelector("#progress").innerHTML = ' ';
            // Loop through each of the selected files.
            for(var i=0;i<files.length;i++) {
				up = document.createElement('div');
				up.id = 'div_upload'+i;

				btn = document.createElement('button');
				btn.onclick = 'Upload.abort('+i+')';
				btn.innerHTML = 'X';

				spn = document.createElement('span');
				spn.id = 'span_upload'+i;

				up.appendChild(btn);
				up.appendChild(spn);
				document.querySelector("#progress").appendChild(up);

				if(i == files.length-1) // 3rd parameter is used for reloading current folder after uploading process
					new Encryption(files[i], Folders.id, i, true);
				else
					new Encryption(files[i], Folders.id, i);
            }
        }
    }
});
