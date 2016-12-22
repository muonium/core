// Decryption module. Loaded in window.onload()
// Compatible with Google Chrome
// Compatible with Mozilla Firefox, Opera thanks to idb.filesystem.js by Eric Bidelman
var Decryption = (function() {
	// Private
	var chunkSize = 1024 * 1024; // Size of one chunk in B
	var CEK = 'password'; // for tests
	var target = 'User';

	var smallQuota = 1024*1024;
	var largeQuota = 1024*1024*1024*100;

	var errorHandler = function(e) {
		console.log("Error");
		console.log(e);
	};

	var time, time_chunk;

	// API
	window.requestFileSystem  = window.requestFileSystem || window.webkitRequestFileSystem;

	// Constructor
	function Decryption(fname, f_id, i) {
		this.folder_id = f_id;
		this.filename = fname;
		this.nb_chk = 0;
		this.enc;
		this.i = i; // Id of file, used only for displaying progress, different from database !
		this.halt = false;
		this.getNbChunks();
	}

	// Public
	Decryption.prototype.fromBitArrayCodec = function(arr) {
		/** Convert from a bitArray to an array of bytes. */
		var out = [], bl = sjcl.bitArray.bitLength(arr), i, tmp;
		for (i=0; i<bl/8; i++) {
			if ((i&3) === 0)
				tmp = arr[i/4];
			out.push(tmp >>> 24);
			tmp <<= 8;
		}
		return out;
	};

	Decryption.prototype.abort = function() {
		this.halt = true;
		var node;
		if(node = document.querySelector("#div_download"+(this.i))) {
			while(node.firstChild) // remove all children
				node.removeChild(node.firstChild);
		}
	};

	Decryption.prototype.getNbChunks = function() {
		var me = this;

		if(this.halt)
			return false;

		time = new Time();
		time.start();
		if(this.filename.length > 0) {
			var xhr = new XMLHttpRequest();
			xhr.open("POST", target+'/getNbChunks', true);
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

			xhr.onreadystatechange = function() {
				if(xhr.status == 200 && xhr.readyState == 4) {
					if(xhr.responseText > 0) {
						me.nb_chk = xhr.responseText;
						console.log("File splitted in "+me.nb_chk+" chunks");
						me.decryptChk(0);
					}
				}
			}
			xhr.send("filename="+this.filename+"&folder_id="+this.folder_id);
		}
	};

	Decryption.prototype.decryptChk = function(line) {
		var me = this;

		if(this.halt)
			return false;

		var chk;
		if(line === undefined)
			line = 0;

		console.log("Decrypting chunk "+(line+1));
		var pct = line/this.nb_chk*100;
		if(pct > 100)
			pct = 100;
		document.querySelector("#span_download"+(this.i)).innerHTML = this.filename+' : '+pct.toFixed(2)+'%';

		time_chunk = new Time();
		var xhr = new XMLHttpRequest();
		xhr.open("POST", target+'/getChunk', true);
		xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

		xhr.onreadystatechange = function() {
			if(xhr.status == 200 && xhr.readyState == 4) {
				if(xhr.responseText != '') {
					time_chunk.stop();
					console.log("Got chunk "+(line+1)+" contents in "+time_chunk.elapsed()+" ms");

					chk = decodeURIComponent(xhr.responseText);

					var split = chk.split(":");
					if (split.length !== 4) {
						throw new sjcl.exception.corrupt("Error :: Incomplete chunk!");
					}
					var c = sjcl.codec.base64.toBits(split[0]);
					var s = sjcl.codec.base64.toBits(split[1]);
					var a = sjcl.codec.base64.toBits(split[2]);
					var i = sjcl.codec.base64.toBits(split[3]);

					if(me.enc === undefined) {
						console.log("Key derivation process...");
						var key = sjcl.misc.pbkdf2(CEK, s, 2000, 256);
						me.enc = new sjcl.cipher.aes(key);
					}

					chk = sjcl.mode.gcm.decrypt(me.enc, c, i, a, 128);

					chk = me.fromBitArrayCodec(chk);
					chk = new Uint8Array(chk);

					// FileSystem API
					// We request large quota (100 GB) to avoid storage errors
					if(window.requestFileSystem === undefined) {
						console.log("Your web browser is currently not supported by Mui app");
					}
					else {
						window.requestFileSystem(
							window.PERSISTENT,
							largeQuota,
							function(fs) {
								fs.root.getFile(me.filename, {create: true}, function(fileEntry) {
									fileEntry.createWriter(function(fileWriter) {
										fileWriter.onwriteend = function(e) {
											// Chunk written
											console.log('Chunk '+(line+1)+'/'+me.nb_chk+' : Write completed.');

											if((line+1) >= me.nb_chk) {
												// All chunks are written
												console.log("Done !");
												time.stop();//
												console.log("decryption + download : "+time.elapsed()+" ms");//
												var node = document.querySelector("#div_download"+(me.i));
												while(node.firstChild) // remove all children
													node.removeChild(node.firstChild);

												// Try to download the file (move from filesystem to download folder)
												if(typeof fileEntry.file === 'function') {
													fileEntry.file(function(file) {
															file = new File([file], me.filename);
															console.log("Creating temp url");
															me.dl(window.URL.createObjectURL(file));
														},
														function() {
															me.dl(fileEntry.toURL(), true);
														}
													);
												}
												else
													me.dl(fileEntry.toURL(), true);
											}
											else {
												// Write next chunk
												me.decryptChk(line+1);
											}
										};

										// Write at the end of the file
										fileWriter.seek(fileWriter.length);
										var blob = new Blob([chk]);
										fileWriter.write(blob);

									}, errorHandler);
								}, function() {
									// If we can't write to filesystem, request a quota
									me.requestQuota(me.decryptChk, me.filename, largeQuota);
								});
							},
							errorHandler
						);
					}
				}
			}
		}
		xhr.send("filename="+this.filename+"&line="+line+"&folder_id="+this.folder_id);
	};

	Decryption.prototype.requestQuota = function(fc, arg, quota) {
		// Usually for Google Chrome
		console.log("Mui cannot download contents for now. Requesting quota...");
		if(navigator.webkitPersistentStorage === undefined) {
			console.log("Your web browser is currently not supported by Mui app");
		}
		else {
			if(quota === undefined)
				quota = smallQuota;

			navigator.webkitPersistentStorage.requestQuota(
				quota,
				function(grantedBytes) {
					console.log("Allowed quota");
					fc(arg);
				},
				function() {
					console.log("Denied quota");
				}
			);
		}
	};

	Decryption.prototype.dl = function(url, feUrl) {
		var me = this;
		console.log("File name : "+this.filename+", url : "+url);

		document.querySelector("#dl_decrypted").href = url;
		document.querySelector("#dl_decrypted").download = this.filename;
		document.querySelector("#dl_decrypted").click();

		// Once downloaded, remove the file in sandbox
		setTimeout(function() {
			me.rm(me.filename);
			if(feUrl === undefined) {
				console.log("Removing temp url");
				window.URL.revokeObjectURL(url);
			}
		}, 2000);
	};

	Decryption.prototype.rm = function(filename) {
		var me = this;

		window.requestFileSystem(
			window.PERSISTENT,
			smallQuota,
			function(fs) {
				fs.root.getFile(filename, {create: false}, function(fileEntry) {
					fileEntry.remove(function() {
						console.log('File removed.');
					}, errorHandler);
				}, function() {
					// If we can't delete to filesystem, request a quota
					me.requestQuota(me.rm, filename, smallQuota);
				});
			},
			errorHandler
		);
	};

	return Decryption;
});
