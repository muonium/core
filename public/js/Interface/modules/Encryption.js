// Encryption module. Loaded in window.onload()
// When page is loaded : Encryption.checkAPI();
var Encryption = (function() {
	// Private
	var chunkSize = 1024 * 1024; // Size of one chunk in B
	var CEK = 'password';
	var target = 'User';

	var errorHandler = function(e) {
		console.log("Error");
		console.log(e);
	};

	var aDATA = sjcl.random.randomWords(1);
	var aDATA = sjcl.codec.base64.fromBits(aDATA);

	// Public
	return {
		checkAPI : function() {
			if(window.File && window.FileReader && window.FileList && window.Blob) {
            	reader = new FileReader();
            	return true;
        	}
			else {
            	alert('The File APIs are not fully supported by your browser. Fallback required.');
            	return false;
        	}
		},

		toBitArrayCodec : function(bytes) {
			/** Convert from an array of bytes to a bitArray. */
			var out = [], i, tmp=0;
			for (i=0; i<bytes.length; i++) {
				tmp = tmp << 8 | bytes[i];
				if ((i&3) === 3) {
					out.push(tmp);
					tmp = 0;
				}
			}
			if (i&3)
				out.push(sjcl.bitArray.partial(8*(i&3), tmp));
			return out;
		},

		read : function(f) {
			Time.start();//
    		var j = 0;
    		console.log('File size : '+f.files[0].size);
    		console.log('File name : '+f.files[0].name);

		    parseFile(f.files[0], {
		        binary: true,
		        chunk_size: chunkSize,
		        success: function(i) {
					// Done
		            Time.stop();//
		    		console.log("split + encryption : "+Time.elapsed()+" ms");//
		            console.log("splitted in "+j+" parts !");
		        },
		        chunk_read_callback: function(chk) {
		            // Reading a chunk
		            chk = new Uint8Array(chk);
		            chk = Encryption.toBitArrayCodec(chk);
		            chk = sjcl.codec.base64.fromBits(chk);
		            var chk_length = Encryption.encryptChk(f.files[0].name, chk);
		            console.log('Part '+j+' size : '+chk_length);
		            j++;
		        },
		        error_callback: errorHandler
		    });
		},

		parseFile : function(file, options) {
		    var opts       = typeof options === 'undefined' ? {} : options;
		    var fileSize   = file.size;
		    var chunkSize  = typeof opts['chunk_size'] === 'undefined' ?  64 * 1024 : parseInt(opts['chunk_size']); // bytes
		    var binary     = typeof opts['binary'] === 'undefined' ? false : opts['binary'] == true;
		    var offset     = 0;
		    var self       = this; // we need a reference to the current object
		    var readBlock  = null;
		    var chunkReadCallback = typeof opts['chunk_read_callback'] === 'function' ? opts['chunk_read_callback'] : function() {};
		    var chunkErrorCallback = typeof opts['error_callback'] === 'function' ? opts['error_callback'] : function() {};
		    var success = typeof opts['success'] === 'function' ? opts['success'] : function() {};

		    var onLoadHandler = function(evt) {
		        var current_chk_length = evt.target.result.length;
		        if(current_chk_length === undefined)
		            current_chk_length = evt.loaded;

		        if (evt.target.error == null && current_chk_length !== undefined) {
		            offset += current_chk_length;
		            chunkReadCallback(evt.target.result);
		        } else {
		            chunkErrorCallback(evt.target.error);
		            return;
		        }
		        if (offset >= fileSize) {
		            success(file);
		            return;
		        }

		        readBlock(offset, chunkSize, file);
		    }

		    readBlock = function(_offset, length, _file) {
		        var r = new FileReader();
		        var blob = _file.slice(_offset, length + _offset);
		        r.onload = onLoadHandler;
		        if (binary) {
		            r.readAsArrayBuffer(blob);
		        } else {
		            r.readAsText(blob);
		        }
		    }
		    readBlock(offset, chunkSize, file);
		},

		encryptChk : function(filename, chk) {
			var s = sjcl.encrypt(CEK, chk, {mode:'gcm', adata:aDATA, iter:2000, ks:256, ts:128} );

		    var xhr = new XMLHttpRequest();
		    xhr.open("POST", target, true);
		    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

		    xhr.onreadystatechange = function() {
		        if(xhr.status == 200 && xhr.readyState == 4) {
		            //console.log(xhr.responseText);
		        }
		    }
		    xhr.send("filename="+filename+"&data="+encodeURIComponent(s));
		    return s.length;
		}
	}
});
