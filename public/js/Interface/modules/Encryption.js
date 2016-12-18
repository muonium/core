// Encryption module. Loaded in window.onload()
// When page is loaded : Encryption.checkAPI();
var Encryption = (function() {
	// Private
	var chunkSize = 1024 * 1024; // Size of one chunk in B
	var CEK = 'password'; // for tests
	var target = 'User';

	var errorHandler = function(e) {
		console.log("Error");
		console.log(e);
	};

	var time;

	// Constructor
	function Encryption(f, f_id) {
		this.folder_id = f_id;
		this.file = f;	// file.name, file.size
		this.j = 0; // Number of chunks read
		this.k = 0; // Number of chunks written

		this.aDATA = sjcl.random.randomWords(1);
		this.initVector = sjcl.random.randomWords(4);
		this.SALT = sjcl.random.randomWords(2);
		this.key = sjcl.misc.pbkdf2(CEK, this.SALT, 2000, 256);
		this.enc = new sjcl.cipher.aes(this.key);

		this.read();
	}

	// Public
	Encryption.prototype.checkAPI = function() {
		if(window.File && window.FileReader && window.FileList && window.Blob) {
			reader = new FileReader();
			return true;
		}
		else {
			alert('The File APIs are not fully supported by your browser. Fallback required.');
			return false;
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

	Encryption.prototype.read = function() {
		var me = this;

		time = new Time();
		console.log('File size : '+this.file.size);
		console.log('File name : '+this.file.name);

		this.parseFile(this.file, {
			binary: true,
			chunk_size: chunkSize,
			success: function(i) {
				// Waiting end of the uploading process
				var timer = setInterval(function() {
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
								console.log("Split + encryption : "+time.elapsed()+" ms");//
								console.log("Splitted in "+me.j+" chunks !");
							}
						}
						xhr.send("filename="+me.file.name+"&data=EOF&folder_id="+me.folder_id);
					}
				}, 1000);
			},
			chunk_read_callback: function(chk) {
				// Reading a chunk
				me.j++;
				chk = new Uint8Array(chk);
				chk = me.toBitArrayCodec(chk);
				var chk_length = me.encryptChk(chk);
				console.log(me.file.name+' - Part '+me.j+' size : '+chk_length);
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
		var me = this;

		var pack = function(c, s, a, i){ //ciphered_chk, salt, authentification data, initialization vector
			var c = sjcl.codec.base64.fromBits(c);
			var s = sjcl.codec.base64.fromBits(s);
			var a = sjcl.codec.base64.fromBits(a);
			var i = sjcl.codec.base64.fromBits(i);
			var t = c+":"+s+":"+a+":"+i;
			return t;
		}

		var s = sjcl.mode.gcm.encrypt(this.enc, chk, this.initVector, this.aDATA, 128);
		s = pack(s, this.SALT, this.aDATA, this.initVector);

		var xhr = new XMLHttpRequest();
		xhr.open("POST", target+'/writeChunk', true);
		xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

		xhr.onreadystatechange = function() {
			if(xhr.status == 200 && xhr.readyState == 4) {
				me.k++;
				//console.log('encryptChk response : '+xhr.responseText);
			}
		}
		xhr.send("filename="+this.file.name+"&data="+encodeURIComponent(s)+"&folder_id="+this.folder_id);
		return s.length;
	};

	return Encryption;
});
