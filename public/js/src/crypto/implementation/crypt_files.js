/*
* @name: fileEncryption()
* @description: encrypt the file before uploading
* @params: file, cek is the content encryption key
* @dependencies: base64.js
* @note: the CEK is there a passphrase, not a key composed of
* the salt and the secrete password and the iteration
* This system allow us to get a different key for all the files
* even if the params are readable.
*/
var fileEncryption = function(file, cek)
{
	var aDATA = sjcl.random.randomWords(3);
	var aDATa = sjcl.codec.base64.fromBits(aDATA);
	var cipheredFile = sjcl.encrypt(cek, file,{
		mode:'gcm', iter:2048, ks:256, ts:128, adata:aDATA
	});
	var cipheredFile = base64.encode(cipheredFile);
	return cipheredFile;
}


/*
* @name: fileDecryption()
* @description: decrypt the file after being downloaded
* @params: file, cek
* @dependencies: base64.js
*/
var fileDecryption = function(file, cek)
{
	var decryptedFile = base64.decode(file);
	var decryptedFile = sjcl.decrypt(cek, file);
	return decryptedFile;
}
