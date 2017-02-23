// Upload module. Loaded in window.onload()
var Upload = (function() {
    // Private
	var f_enc = [];

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

        upFiles : function(files) {
			var up, btn, spn;
            document.querySelector("#progress").innerHTML = ' ';
            // Loop through each of the selected files.
            for(var i=0;i<files.length;i++) {
				up = document.createElement('div');
				up.id = 'div_upload'+i;

				btn = document.createElement('button');
				btn.setAttribute('data-id', i);
				btn.onclick = Upload.abort;
				btn.innerHTML = '- X -';

				spn = document.createElement('span');
				spn.id = 'span_upload'+i;

				up.appendChild(btn);
				up.appendChild(spn);
				document.querySelector("#progress").appendChild(up);

				if(i == files.length-1) // 3rd parameter is used for reloading current folder after uploading process
					f_enc[i] = new Encryption(files[i], Folders.id, i, true);
				else
					f_enc[i] = new Encryption(files[i], Folders.id, i);
            }
        }
    }
});
