var genCek = {};
genCek.plt = function(){
	var key = sjcl.random.randomWords(4); //get a random value
	var key = sjcl.codec.base64.fromBits(key); //the base64 encoding will be the CEK itself
	return key;
}

genCek.enc = function(key, passphrase){
	var a = sjcl.random.randomWords(1);
	//encrypt it
	var key = sjcl.encrypt(passphrase, key, {mode:'gcm', iter:2000, ks:256, a, ts:128});
	var key = base64.encode(key); //don't store a Json in mongoDB...
	return key;
}

genCek.dec = function(key, passphrase){
	var cek = base64.decode(key);
	var cek = sjcl.decrypt(passphrase, cek);
	return cek;
}
