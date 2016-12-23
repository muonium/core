var genCek = {};
genCek.plt = function(){
	var key = sjcl.random.randomWords(4); //get a random value
	return key;
}

genCek.enc = function(key, passphrase){
	var a = sjcl.random.randomWords(1);
	var i = sjcl.random.randomWords(4);
	var s = sjcl.random.randomWords(2);
	//encrypt it
	var key = sjcl.encrypt(passphrase, key, {mode:'gcm', iv:i, salt:s, iter:2000, ks:256, adata:a, ts:128});
	var key = base64.encode(key); //don't store a Json in mongoDB...
	return key;
}

genCek.dec = function(key, passphrase){
	var cek = base64.decode(key);
	var cek = sjcl.decrypt(passphrase, cek);
	return cek;
}
