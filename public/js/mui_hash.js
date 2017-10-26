var hex2chars = function(input) {
    var output = '';
    input = input.replace(/^(0x)?/g, '');
    input = input.replace(/[^A-Fa-f0-9]/g, '');
    input = input.split('');
    for(var i = 0; i < input.length; i+=2) {
        output += String.fromCharCode(parseInt(input[i]+''+input[i+1], 16));
    }
    return output;
}

var mui_hash = function(input) {
    // Hash a string in sha384 thanks to sha512.js, convert it in base64 thanks to base64.js and urlencode it
    return encodeURIComponent(base64.encode(hex2chars(sha384(input)), true));
}
