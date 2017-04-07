// Encryption module. Loaded in window.onload()
// When page is loaded : Encryption.checkAPI();
var Encryption = (function() {
	// Private
	var chunkSize = 1024 * 1024; // Size of one chunk in B
	var cek = sessionStorage.getItem("cek");
	if (cek == null) { //check if the cek is there
		sessionStorage.clear();
		window.location.href = ROOT+"Logout"; //doesn't exist ? Then logout the user
	}
	var target = 'User';
	var est = 33.5; // Estimation of the difference between the file and encrypted file in %
	var debug = false;

	var errorHandler = function(e) {
		console.log("Error");
		console.log(e);
	};

	var time;

	// Constructor
	function Encryption(f, f_id, i, callback) {
		var me = this;
		document.querySelector("#span_upload"+i).innerHTML = f.name+' : 0%';

		this.callback = callback;
		this.folder_id = f_id;
		this.file = f;	// file.name, file.size
		this.est_size = Math.round(f.size*(1+est/100)); // Estimation of encrypted file size
		this.i = i; // Id of file, used only for displaying progress, different from database !
		this.j = 0; // Number of chunks read
		this.k = 0; // Number of chunks written
		this.l = 0; // Number of B written
		this.m = 0; // Start to write at chunk x
		this.halt = false;

		//crypto parameters
		this.salt = sjcl.random.randomWords(2);

		//key derivation
		this.key = sjcl.misc.pbkdf2(cek, this.salt, 7000, 256);
		this.enc = new sjcl.cipher.aes(this.key);

		var replaceYesAction = function() {
			var file_id = document.querySelector('span.file[data-title="'+f.name+'"]').id;
			if(file_id) {
				Rm.rm(file_id, function(){Upload.read(me.i)}, false);
			} else { alert('Error'); }
		}

		var completeYesAction = function(chkNb) {
			var file_id = document.querySelector('span.file[data-title="'+f.name+'"]').id;
			if(file_id) {
				Upload.read(me.i, chkNb);
			} else { alert('Error'); }
		}

		var noAction = function() {
			// Remove file from uploading files
			me.abort();
		}

		// Check status before uploading
		var xhr = new XMLHttpRequest();
		xhr.open("POST", target+'/getFileStatus', true);
		xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

		xhr.onreadystatechange = function() {
			// TODO : improve alert (only one) and confirm (yes, no, yes for all, no for all)
			if(xhr.status == 200 && xhr.readyState == 4) {
				var filestatus = xhr.responseText.split('@');
				console.log(filestatus);
				if(filestatus[0] == '0') { // File doesn't exist, it's ok
					me.read();
				}
				else if(filestatus[0] == '1' && filestatus.length === 2 && Upload.yesCompleteAll === true) {
					if(isNumeric(filestatus[1]))
						completeYesAction(filestatus[1]);
				}
				else if((filestatus[0] == '1' || filestatus[0] == '2') && Upload.yesReplaceAll === true) {
					replaceYesAction();
				}
				else if((filestatus[0] == '1' || filestatus[0] == '2') && Upload.noAll === true) {
					noAction();
				}
				else if(filestatus[0] == '1' && filestatus.length === 2) { // File exists and not completed
					if(isNumeric(filestatus[1])) {
						var c = false;
						if(typeof me.callback != 'function') { // Only one file or this is the last file
							var m = new MessageBox(txt.User.replaceFile.replace('[filename]', f.name))
								.addToggle(txt.User.complete, txt.User.replace, function() {
									c = true;
								})
								.addButton(txt.User.yes, function() {
									if(c)
										replaceYesAction();
									else
										completeYesAction(filestatus[1]);
								})
								.addButton(txt.User.no, noAction)
								.show();
						}
						else {
							var m = new MessageBox(txt.User.replaceCompleteFile.replace('[filename]', f.name))
								.addToggle(txt.User.complete, txt.User.replace, function() {
									c = true;
								})
								.addButton(txt.User.yes, function() {
									if(c)
										replaceYesAction();
									else
										completeYesAction(filestatus[1]);
								})
							    .addButton(txt.User.yesAll, function() {
									if(c) {
										Upload.yesReplaceAll = true;
										replaceYesAction();
									}
									else {
										Upload.yesCompleteAll = true;
										completeYesAction(filestatus[1]);
									}
							    })
							    .addButton(txt.User.no, noAction)
							    .addButton(txt.User.noAll, function() {
									Upload.noAll = true;
									noAction();
							    })
							    .show();
						}
					}
				}
				else if(filestatus[0] == '2') { // File exists
					if(typeof me.callback != 'function') { // Only one file or this is the last file
						var m = new MessageBox(txt.User.replaceFile.replace('[filename]', f.name)).addButton('Yes', replaceYesAction).addButton('No', noAction).show();
					}
					else {
						var m = new MessageBox(txt.User.replaceFile.replace('[filename]', f.name))
							.addButton(txt.User.yes, replaceYesAction)
							.addButton(txt.User.yesAll, function() {
								Upload.yesReplaceAll = true;
								replaceYesAction();
							})
							.addButton(txt.User.no, noAction)
							.addButton(txt.User.noAll, function() {
								Upload.noAll = true;
								noAction();
							})
							.show();
					}
				}
				else if(filestatus[0] == 'quota') {
					alert(txt.User.quotaExceeded);
				}
				else {
					alert('Error');
				}
			}
		}
		xhr.send("filename="+f.name+"&filesize="+f.size+"&folder_id="+f_id);
	}

	// Public
	Encryption.prototype.checkAPI = function() {
		if(window.File && window.FileReader && window.FileList && window.Blob) {
			reader = new FileReader();
			return true;
		}
		else {
			alert(txt.User.fileAPI);
			return false;
		}
	};

	Encryption.prototype.abort = function() {
		var me = this;
		Transfers.number--;
		Transfers.numberUp--;

		me.halt = true;
		var node = document.querySelector("#div_upload"+(me.i));
		if(node) {
			while(node.firstChild) // remove all children
				node.removeChild(node.firstChild);
		}

		/* Reload current folder to show aborted file in tree */
		Folders.open(Folders.id);

		if(typeof me.callback == 'function') {
			me.callback();
		}
	};

	Encryption.prototype.toBitArrayCodec = function(bytes) {
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
	};

	Encryption.prototype.read = function(chkNb = 0) {
		console.log(chkNb);
		this.m = chkNb;
		var me = this;
		Transfers.number++;
		Transfers.numberUp++;

		if(typeof me.callback == 'function') {
			me.callback();
		}

		time = new Time();
		if(debug) {
			console.log('File size : '+this.file.size);
			console.log('File name : '+this.file.name);
		}

		this.parseFile(this.file, {
			binary: true,
			chunk_size: chunkSize,
			success: function(i) {
				if(me.halt)
					return false;
				// Waiting end of the uploading process
				var timer = setInterval(function() {
					if(debug)
						console.log("Waiting...");
					if(me.k >= me.j) {
						// Done, write "EOF" at the end of file
						clearInterval(timer);

						var xhr = new XMLHttpRequest();
						xhr.open("POST", target+'/writeChunk', true);
						xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

						xhr.onreadystatechange = function() {
							if(xhr.status == 200 && xhr.readyState == 4) {
								time.stop();//
								Transfers.number--;
								Transfers.numberUp--;
								var node = document.querySelector("#div_upload"+(me.i));
								while(node.firstChild) // remove all children
									node.removeChild(node.firstChild);
								if(debug) {
									console.log("Split + encryption : "+time.elapsed()+" ms");//
									console.log("Splitted in "+me.j+" chunks !");
								}
								if(typeof me.callback != 'function') {
									Folders.open(me.folder_id);
								}
							}
						}
						xhr.send("filename="+me.file.name+"&data=EOF&folder_id="+me.folder_id);
					}
				}, 1000);
			},
			chunk_read_callback: function(chk) {
				// Reading a chunk
				if(me.halt)
					return false;
				me.j++;
				if(me.m < me.j) {
					chk = new Uint8Array(chk);
					chk = me.toBitArrayCodec(chk);
					var chk_length = me.encryptChk(chk);
					if(debug)
						console.log(me.file.name+' - Part '+me.j+' size : '+chk_length);
				}
				else {
					me.k++;
					me.l += Math.round(chunkSize*(1+est/100));
					var pct = me.l/me.est_size*100;
					if(pct > 100)
						pct = 100;
					document.querySelector("#span_upload"+(me.i)).innerHTML = me.file.name+' : '+pct.toFixed(2)+'%';
					console.log('Did not write part '+me.j);
				}
			},
			error_callback: errorHandler
		});
	};

	Encryption.prototype.parseFile = function(file, options) {
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
	};

	Encryption.prototype.encryptChk = function(chk) {
		if(this.halt)
			return false;
		var me = this;

		var pack = function(c, s, a, i){ //ciphered_chk, salt, authentification data, initialization vector
			var c = sjcl.codec.base64.fromBits(c);
			var s = sjcl.codec.base64.fromBits(s);
			var a = sjcl.codec.base64.fromBits(a);
			var i = sjcl.codec.base64.fromBits(i);
			var t = c+":"+s+":"+a+":"+i;
			return t;
		}

		//crypto parameter
		var initVector = sjcl.random.randomWords(4);
		var aDATA = sjcl.random.randomWords(4);

		//chunk encryption
		var s = sjcl.mode.gcm.encrypt(this.enc, chk, initVector, aDATA, 128);
		s = pack(s, this.salt, aDATA, initVector);

		var xhr = new XMLHttpRequest();
		xhr.open("POST", target+'/writeChunk', true);
		xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

		xhr.onreadystatechange = function() {
			if(xhr.status == 200 && xhr.readyState == 4 && me.halt === false) {
				console.log(xhr.responseText);
				me.l += s.length;
				var pct = me.l/me.est_size*100;
				if(pct > 100)
					pct = 100;
				document.querySelector("#span_upload"+(me.i)).innerHTML = me.file.name+' : '+pct.toFixed(2)+'%';

				if(xhr.responseText == 'error') {
					// Quota exceeded or unable to write
					console.log('unable to write');
					me.halt = true;
					return false;
				}
				me.k++;
			}
		}
		xhr.send("filename="+this.file.name+"&data="+encodeURIComponent(s)+"&folder_id="+this.folder_id);
		return s.length;
	};

	return Encryption;
});
