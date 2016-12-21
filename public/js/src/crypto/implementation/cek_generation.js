/*
* @name: cekGeneration()
* @description: generation of the content encryption key,
* @params: 'passphrase' is the user's passphrase or secrete password, call this like you want to...
* CEK means "Content Encryption Key"
* the CEK is encrypted by the passphrase of the user
* @note: the CEK is just a random secrete password,
* and pbkdf2 internally in SJCL derivate a key from the CEK
* @dependencies: crypt_cek.js, base64.js
*/
var cekGeneration = function(passphrase)
{
	var contentEncryptionKey = sjcl.random.randomWords(8); //256 bits key size (one word = 32 bits)
	var contentEncryptionKey = sjcl.codec.base64.fromBits(contentEncryptionKey); //convert the key as a b64 encoded string to get the CEK we want
	console.log("CEK generated!\n\
	More details about the cryptography implementation at https://github.com/muonium/core/wiki/Crypto-details");
	var contentEncryptionKey = base64.encode(contentEncryptionKey); //convert it one more time because cekEncryption() askes it
	return cekEncryption(passphrase, contentEncryptionKey); //cekEncryption() come from js/implementation/crypt_cek.js
}
