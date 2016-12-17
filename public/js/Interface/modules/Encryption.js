// Encryption module. Loaded in window.onload()
// When page is loaded : Encryption.checkAPI();
var Encryption = (function() {
	// Private
	var chunkSize = 1024 * 1024; // Size of one chunk in B
	var CEK = 'password'; // for tests
	var target = 'User';
	var folder_id;
	var j = 0; // Number of chunks read
	var k = 0; // Number of chunks written

	var errorHandler = function(e) {
		console.log("Error");
		console.log(e);
	};

	var aDATA = sjcl.random.randomWords(1);
	var aDATA = sjcl.codec.base64.fromBits(aDATA);

	var initVector = sjcl.random.randomWords(4);

	var SALT = sjcl.random.randomWords(2);

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
			folder_id = Folders.id;
			Time.start();//
    		j = 0; k = 0;
    		console.log('File size : '+f.size);
    		console.log('File name : '+f.name);

		    Encryption.parseFile(f, {
		        binary: true,
		        chunk_size: chunkSize,
		        success: function(i) {
					// Waiting end of the uploading process
		            var timer = setInterval(function() {
		                console.log("Waiting...");
		                if(k >= j) {
							// Done, write "EOF" at the end of file
		                    clearInterval(timer);

							var xhr = new XMLHttpRequest();
						    xhr.open("POST", target+'/writeChunk', true);
						    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

						    xhr.onreadystatechange = function() {
						        if(xhr.status == 200 && xhr.readyState == 4) {
									Time.stop();//
									console.log("Split + encryption : "+Time.elapsed()+" ms");//
									console.log("Splitted in "+j+" chunks !");
						        }
						    }
						    xhr.send("filename="+f.name+"&data=EOF&folder_id="+folder_id);
		                }
		            }, 1000);
		        },
		        chunk_read_callback: function(chk) {
		            // Reading a chunk
					j++;
		            chk = new Uint8Array(chk);
		            chk = Encryption.toBitArrayCodec(chk);
		            chk = sjcl.codec.base64.fromBits(chk);
		            var chk_length = Encryption.encryptChk(f.name, chk);
		            console.log('Part '+j+' size : '+chk_length);
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
			var s = sjcl.encrypt(CEK, chk, {mode:'gcm', adata:aDATA, iter:2000, ks:256, ts:128, salt:SALT, iv:initVector});

		    var xhr = new XMLHttpRequest();
		    xhr.open("POST", target+'/writeChunk', true);
		    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

		    xhr.onreadystatechange = function() {
		        if(xhr.status == 200 && xhr.readyState == 4) {
					k++;
		            //console.log('encryptChk response : '+xhr.responseText);
		        }
		    }
		    xhr.send("filename="+filename+"&data="+encodeURIComponent(s)+"&folder_id="+folder_id);
		    return s.length;
		}
	}
});
