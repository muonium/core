var genCek = function(){
	var key = sjcl.random.randomWords(4); //get a random value
	var key = sjcl.codec.base64.fromBits(key); //the base64 encoding will be the CEK itself
	return key;
}
