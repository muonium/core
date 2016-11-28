/*
* @name: cekGeneration()
* @description: generation of the content encryption key,
* CEK means "Content Encryption Key"
*/
var cekGeneration = function()
{
	var password = sjcl.random.randomWords(32);
	var password = sjcl.codec.base64.fromBits(password);
	var salt = sjcl.random.randomWords(32);
	var salt = sjcl.codec.base64.fromBits(salt);
	var contentEncryptionKey = sjcl.misc.pbkdf2(password, salt, 4096, 256); //4096 : iteration, 256 : key size
	var contentEncryptionKey = sjcl.codec.base64.fromBits(contentEncryptionKey);
	console.log("CEK generated!");
	return contentEncryptionKey;
}
