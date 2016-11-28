/*
* @name: fileEncryption()
* @description: encrypt the file before uploading
* @params: file, cek is the content encryption key
*/
var fileEncryption = function(file, cek)
{
	var aDATA = sjcl.random.randomWords(3);
	var aDATa = sjcl.codec.base64.fromBits(aDATA);
	var cipheredFile = sjcl.encrypt(cek, file,{
		mode:'gcm', iter:2048, ks:256, ts:128, adata:aDATA
	});
	return cipheredFile;
}


/*
* @name: fileDecryption()
* @description: decrypt the file after being downloaded
* @params: file, cek
*/
var fileDecryption = function(file, cek)
{
	var decryptedFile = sjcl.decrypt(cek, file);
	return decryptedFile;
}
