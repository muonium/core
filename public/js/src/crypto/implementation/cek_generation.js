/*
* @name: cekGeneration()
* @description: generation of the content encryption key,
* @params: 'passphrase' is the user's passphrase or secrete password, call this like you want to...
* CEK means "Content Encryption Key"
* the CEK is encrypted by the passphrase of the user
* @dependencies: crypt_cek.js
*/
var cekGeneration = function(passphrase)
{
	var contentEncryptionKey = sjcl.random.randomWords(4); //256 bits key size
	var contentEncryptionKey = sjcl.codec.base64.fromBits(contentEncryptionKey);
	console.log("CEK generated!");
	return cekEncryption(passphrase, contentEncryptionKey); //cekEncryption() come from js/implementation/crypt_cek.js
}
