// Decryption module. Loaded in window.onload()
// Compatible with Google Chrome
// Compatible with Mozilla Firefox, Opera thanks to idb.filesystem.js by Eric Bidelman
var Decryption = (function() {
	// Private
	var chunkSize = 1024 * 1024; // Size of one chunk in B
	var CEK = 'password'; // for tests
	var target = 'User';
	var folder_id;

	var smallQuota = 1024*1024;
	var largeQuota = 1024*1024*1024*100;

	var errorHandler = function(e) {
		console.log("Error");
		console.log(e);
	};

	var nb_chk = 0, fname;

	// API
    window.requestFileSystem  = window.requestFileSystem || window.webkitRequestFileSystem;

	// Public
	return {
		fromBitArrayCodec : function(arr) {
			/** Convert from a bitArray to an array of bytes. */
		    var out = [], bl = sjcl.bitArray.bitLength(arr), i, tmp;
		    for (i=0; i<bl/8; i++) {
		        if ((i&3) === 0)
		            tmp = arr[i/4];
		        out.push(tmp >>> 24);
		        tmp <<= 8;
		    }
		    return out;
		},

		getNbChunks : function(filename, f_id) {
    		Time.start();//
			folder_id = f_id;
		    if(filename.length > 0) {
		        var xhr = new XMLHttpRequest();
		        xhr.open("POST", target+'/getNbChunks', true);
		        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

		        xhr.onreadystatechange = function() {
		            if(xhr.status == 200 && xhr.readyState == 4) {
		                if(xhr.responseText > 0) {
		                    nb_chk = xhr.responseText;
							console.log("File splitted in "+nb_chk+" chunks");
		                    Decryption.decryptChk(filename, 0); // TODO : edit this line
		                }
		            }
		        }
		        xhr.send("filename="+filename+"&folder_id="+folder_id); // TODO : edit this line
		    }
		},

		decryptChk : function(filename, line) {
			var chk;
			if(line === undefined)
				line = 0;

		    console.log("Decrypting chunk "+(line+1));
		    var xhr = new XMLHttpRequest();
		    xhr.open("POST", target+'/getChunk', true);
		    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

		    xhr.onreadystatechange = function() {
		        if(xhr.status == 200 && xhr.readyState == 4) {
		            if(xhr.responseText != '') {
		                console.log("Got chunk "+(line+1)+" contents");

		                chk = decodeURIComponent(xhr.responseText);
		                chk = sjcl.decrypt(CEK, chk);
		                chk = sjcl.codec.base64.toBits(chk);
		                chk = Decryption.fromBitArrayCodec(chk);
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
		                            fs.root.getFile(filename, {create: true}, function(fileEntry) {
		                                fileEntry.createWriter(function(fileWriter) {
		                                    fileWriter.onwriteend = function(e) {
												// Chunk written
		                                        console.log('Chunk '+(line+1)+'/'+nb_chk+' : Write completed.');

		                                        if((line+1) >= nb_chk) {
													// All chunks are written
		                                            console.log("Done !");
		                                            Time.stop();//
													console.log("decryption + download : "+Time.elapsed()+" ms");//

													// Try to download the file (move from filesystem to download folder)
													fname = filename;
													if(typeof fileEntry.file === 'function') {
														fileEntry.file(Decryption.file2url,
														function() {
															Decryption.dl(filename, fileEntry.toURL(), true);
														});
													}
													else
														Decryption.dl(filename, fileEntry.toURL(), true);
		                                        }
		                                        else {
													// Write next chunk
		                                            Decryption.decryptChk(filename, line+1);
		                                        }
		                                    };

											// Write at the end of the file
		                                    fileWriter.seek(fileWriter.length);
		                                    var blob = new Blob([chk]);
		                                    fileWriter.write(blob);

		                                }, errorHandler);
		                            }, function() {
										// If we can't write to filesystem, request a quota
										Decryption.requestQuota(Decryption.decryptChk, filename, largeQuota);
									});
		                        },
		                        errorHandler
		                    );
						}
		            }
		        }
		    }
		    xhr.send("filename="+filename+"&line="+line+"&folder_id="+folder_id);
		},

		requestQuota : function(fc, arg, quota) {
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
		},

		file2url : function(file) {
			file = new File([file], fname);
			console.log("Creating temp url");
			Decryption.dl(fname, window.URL.createObjectURL(file));
		},

		dl : function(filename, url, feUrl) {
			console.log("File name : "+filename+", url : "+url);

			document.querySelector("#dl_decrypted").href = url;
		    document.querySelector("#dl_decrypted").download = filename;
		    document.querySelector("#dl_decrypted").click();

			// Once downloaded, remove the file in sandbox
			Decryption.rm(filename);
			if(feUrl === undefined) {
				console.log("Removing temp url");
				//window.URL.revokeObjectURL(url);
			}
		},

		rm : function(filename) {
			if(filename === undefined) { // TODO update these lines
				if(document.querySelector("#delete").value.length > 0)
					var filename = document.querySelector("#delete").value;
				else
					return;
			}

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
						Decryption.requestQuota(Decryption.rm, filename, smallQuota);
					});
		        },
				errorHandler
		    );
		}
	}
});
