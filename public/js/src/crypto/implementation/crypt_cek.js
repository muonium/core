
/*
* @name: cekEncryption()
* @description: encrypt the cek before send it to the server
* @params: user's passphrase and user's CEK
*/
var cekEncryption = function(passphrase, cek)
{
	var aDATA = sjcl.random.randomWords(3);
	var aDATA = sjcl.codec.base64.fromBits(aDATA);
	var encryptedCEK = sjcl.encrypt(passphrase, cek,{
		mode:'gcm', ks:256, ts:128, iter:2048, adata:aDATA
	});
	var encryptedCEK = base64.encode(encryptedCEK);
	return encryptedCEK;
}

/*
* @name: cekDecryption()
* @description: decrypt the CEK after downloaded the encrypted CEK
* @params: passphrase, cek
*/
var cekDecryption = function(passphrase, cek)
{
	var decryptedCEK = base64.decode(cek)
	var decryptedCEK = sjcl.decrypt(passphrase, decryptedCEK);
	return decryptedCEK;
}
