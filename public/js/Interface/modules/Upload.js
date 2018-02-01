// Upload module. Loaded in window.onload()
var Upload = (function() {
    // Private
	var f_enc = [];
	var f_files;

    // Public
    return {
        dialog : function(e) {
			if(typeof e === 'object' && e !== null) e.preventDefault();
            $('#upFilesInput').trigger('click');
        },

        abort : function() {
			var i = this.getAttribute('data-id');
			console.log("Aborting "+i);
			f_enc[i].abort();
        },

		read : function(i, chkNb = 0) {
			f_enc[i].read(chkNb);
		},

		upFile : function(file_id) {
			console.log("Uploading file "+file_id+"/"+(f_files.length-1));
			var fname = f_files[file_id].name !== undefined ? f_files[file_id].name : '';
			var ficon = ExtIcons.set(fname);

			$('.transfers_upload').contents().filter(function() {
    			return (this.nodeType == 3);
			}).remove();
			$('.transfers_upload').append('<div id="div_upload'+ file_id +'">'+
				'<i data-id="'+ file_id +'" class="fa fa-times-circle-o btn-abort" aria-hidden="true"></i>'+
				'<div>'+
					'<span class="fileinfo">'+ ficon + fname +'</span>'+
					'<span class="pct">0%</span>' +
					'<div class="progress_bar"><div class="used" style="width:0%"></div></div>'+
				'</div>'+
			'</div>');

			$('#div_upload'+file_id+' .btn-abort').on('click', Upload.abort);

			if(file_id == f_files.length-1) {
				f_enc[file_id] = new Encryption(f_files[file_id], Folders.id, file_id, null);
			}
			else {
				f_enc[file_id] = new Encryption(f_files[file_id], Folders.id, file_id, function() {
					Upload.upFile(file_id + 1);
				});
			}
		},

        upFiles : function(files) {
			f_files = files;
			$('.transfers_upload').html(' ');
			Transfers.open();
			Transfers.showUp();
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
