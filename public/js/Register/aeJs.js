/**
* @author           : Romain Claveau
* @description      : Implentation of AES algorithm in Javascript
* @license          : Apache 2.0
* @documentation    : - http://asmaes.sourceforge.net/rijndael/rijndaelImplementation.pdf
*                     - http://csrc.nist.gov/publications/fips/fips197/fips-197.pdf
*                     - https://developer.mozilla.org/en-US/docs/Web/JavaScript/Guide/Expressions_and_Operators
*                     - http://fr.slideshare.net/hisunilkumarr/advanced-encryption-sta
*                     - https://en.wikipedia.org/wiki/Advanced_Encryption_Standard
*/
'use strict';

var aes = {};
aes.actions = {};

/**
* @name         : sBox
* @description  : Substitution values for the SubBytes() transformation [§5.1.1]
*/
aes.sBox = [
    0x63,0x7c,0x77,0x7b,0xf2,0x6b,0x6f,0xc5,0x30,0x01,0x67,0x2b,0xfe,0xd7,0xab,0x76,
    0xca,0x82,0xc9,0x7d,0xfa,0x59,0x47,0xf0,0xad,0xd4,0xa2,0xaf,0x9c,0xa4,0x72,0xc0,
    0xb7,0xfd,0x93,0x26,0x36,0x3f,0xf7,0xcc,0x34,0xa5,0xe5,0xf1,0x71,0xd8,0x31,0x15,
    0x04,0xc7,0x23,0xc3,0x18,0x96,0x05,0x9a,0x07,0x12,0x80,0xe2,0xeb,0x27,0xb2,0x75,
    0x09,0x83,0x2c,0x1a,0x1b,0x6e,0x5a,0xa0,0x52,0x3b,0xd6,0xb3,0x29,0xe3,0x2f,0x84,
    0x53,0xd1,0x00,0xed,0x20,0xfc,0xb1,0x5b,0x6a,0xcb,0xbe,0x39,0x4a,0x4c,0x58,0xcf,
    0xd0,0xef,0xaa,0xfb,0x43,0x4d,0x33,0x85,0x45,0xf9,0x02,0x7f,0x50,0x3c,0x9f,0xa8,
    0x51,0xa3,0x40,0x8f,0x92,0x9d,0x38,0xf5,0xbc,0xb6,0xda,0x21,0x10,0xff,0xf3,0xd2,
    0xcd,0x0c,0x13,0xec,0x5f,0x97,0x44,0x17,0xc4,0xa7,0x7e,0x3d,0x64,0x5d,0x19,0x73,
    0x60,0x81,0x4f,0xdc,0x22,0x2a,0x90,0x88,0x46,0xee,0xb8,0x14,0xde,0x5e,0x0b,0xdb,
    0xe0,0x32,0x3a,0x0a,0x49,0x06,0x24,0x5c,0xc2,0xd3,0xac,0x62,0x91,0x95,0xe4,0x79,
    0xe7,0xc8,0x37,0x6d,0x8d,0xd5,0x4e,0xa9,0x6c,0x56,0xf4,0xea,0x65,0x7a,0xae,0x08,
    0xba,0x78,0x25,0x2e,0x1c,0xa6,0xb4,0xc6,0xe8,0xdd,0x74,0x1f,0x4b,0xbd,0x8b,0x8a,
    0x70,0x3e,0xb5,0x66,0x48,0x03,0xf6,0x0e,0x61,0x35,0x57,0xb9,0x86,0xc1,0x1d,0x9e,
    0xe1,0xf8,0x98,0x11,0x69,0xd9,0x8e,0x94,0x9b,0x1e,0x87,0xe9,0xce,0x55,0x28,0xdf,
    0x8c,0xa1,0x89,0x0d,0xbf,0xe6,0x42,0x68,0x41,0x99,0x2d,0x0f,0xb0,0x54,0xbb,0x16
];

aes.rCon = [
    [0x00, 0x00, 0x00, 0x00],
    [0x01, 0x00, 0x00, 0x00],
    [0x02, 0x00, 0x00, 0x00],
    [0x04, 0x00, 0x00, 0x00],
    [0x08, 0x00, 0x00, 0x00],
    [0x10, 0x00, 0x00, 0x00],
    [0x20, 0x00, 0x00, 0x00],
    [0x40, 0x00, 0x00, 0x00],
    [0x80, 0x00, 0x00, 0x00],
    [0x1b, 0x00, 0x00, 0x00],
    [0x36, 0x00, 0x00, 0x00]
];

aes.FFTab = [
    0x00, 0x1b, 0x36, 0x2d, 0x6c, 0x77, 0x5a, 0x41
];

/**
* @name         : AES Cipher function
* @description  : Encryption of "input" state using Rijndael algorithm
* @section      : §5.1
* @params       : input[4*Nb]   : Input state array
*                 w[Nb][Nr+1]   : Key schedule array
* @returns      : output[]      : Output new State array
*/
aes.cipher = function(input, w)
{
    // Number of round (depend of key size) : 10 (128-bits key) - 12 (192-bits key) - 14 (256-bits key)
    var Nr = w.length / 4 - 1;

    // Initialisation of the State array
    var state = [[], [], [], []];

    // Fill State array with input content [§3.4]
    for(var i = 0; i < 16; i++)
    {
        state[i % 4][Math.floor(i / 4)] = input[i];
    }

    // First AddRoundKey() transformation [§5.1.4]
    state = aes.addRoundKey(state, w, 0);

    // Beginning of the loop of transformations
    for(var round = 1; round < Nr; round++)
    {
        // SubBytes() transformation [§5.1.1]
        state = aes.subBytes(state);

        // ShiftRows() transformation [§5.1.2]
        state = aes.shiftRows(state);

        // MixColumns() transformation [§5.1.3]
        state = aes.mixColumns(state);

        // Other AddRoundKey() transformation [§5.1.4]
        state = aes.addRoundKey(state, w, round);
    }

    // Final SubBytes() transformation [§5.1.1]
    state = aes.subBytes(state);

    // Final ShiftRows() transformation [§5.1.2]
    state = aes.shiftRows(state);

    // Final AddRoundKey() transformation [§5.1.4]
    state = aes.addRoundKey(state, w, Nr);

    // Converting the State array before returning it
    var out = new Array(16);

    for(var i = 0; i < 16; i++)
    {
        out[i] = state[i % 4][Math.floor(i/4)];
    }

    return out;
};

/**
* @name         : KeyExpansion transformation
* @description  : AES takes the Cipher Key and performs Key Expansion routine to generate a key schedule
* @section      : §5.2
* @params       : key[Nb * Nk]  : Cipher Key array
* @returns      : w[Nb] : Key Expansion
*/
aes.keyExpansion = function(key)
{
    var temp = new Array(4),
        Nk = key.length / 4,
        Nr = Nk + 6,
        w = new Array(4 * (Nr + 1));
        i = 0;

    for(; i < Nk; i++)
    {
        w[i] = [key[4 * i], key[4 * i + 1], key[4 * i + 2], key[4 * i + 3]];
    }
    
    var i = Nk;
    
    for(; i < (4 * (Nr + 1)); i++)
    {
        w[i] = new Array(4);

        for(var a = 0; a < 4; a++)
        {
            temp[a] = w[i - 1][a];
        }

        if(i % Nk === 0)
        {
            temp = aes.subWord(aes.rotWord(temp));

            for(var a = 0; a < 4; a++)
            {
                temp[a] ^= aes.rCon[i / Nk][a];
            }
        }
        else if(Nk > 6 && i % Nk === 4)
        {
            temp = aes.subWord(temp);
        }

        for(var a = 0; a < 4; a++)
        {
            w[i][a] = w[i - Nk][a] ^ temp[a];
        }
    }

    return w;
};

/**
* @name         : AddRoundKey transformation
* @description  : In the AddRoundKey() transformation, a round key is added to the State by a XOR operation
* @section      : §5.1.4
* @params       : state[Nb][Nb] : State array
                  w[Nb][Nr+1]   : Key schedule array
                  round         : Number of transformation of the Cipher
                  Nb            : Number of round (view description of cipher function for more information)
* @returns      : state[Nb][Nb] : Output of the State array after addRoundKey() transformation
*/
aes.addRoundKey = function(state, w, round)
{
    for(var row = 0; row < 4; row++)
    {
        for(var col = 0; col < 4; col++)
        {
            // XOR operation (`^` symbolize the XOR operation)
            state[row][col] ^= w[round * 4 + col][row];
        }
    }

    return state;
};

/**
* @name         : SubBytes transformation
* @description  : The SubBytes() transformation is a non-linear byte substitution that operates independently on each byte of the State using a substitution table, the S-Box
* @section      : §5.1.1
* @params       : state[Nb][Nb] : State array
                  Nb            : Number of round (view description of cipher function for more information)
* @returns      : state[Nb][Nb] : Output of the State array after subBytes() transformation
*/
aes.subBytes = function(state)
{
    for(var row = 0; row < 4; row++)
    {
        for(var col = 0; col < 4; col++)
        {
            // Substitution using the S-Box
            state[row][col] = aes.sBox[state[row][col]];
        }
    }

    return state;
};

/**
* @name         : ShiftRows transformation
* @description  : The ShiftRows() transformation is a cyclic shifting of the bytes in the last three rows of the State array
* @section      : §5.1.2
* @params       : state[Nb][Nb] : State array
                  Nb            : Number of round (view description of cipher function for more information)
* @returns      : state[Nb][Nb] : Output of the State array after shiftRows() transformation
*/
aes.shiftRows = function(state)
{
    var temp = new Array(4);

    for(var row = 1; row < 4; row++)
    {
        temp[0] = state[row][(row + 0) % 4];
        temp[1] = state[row][(row + 1) % 4];
        temp[2] = state[row][(row + 2) % 4];
        temp[3] = state[row][(row + 3) % 4];

        state[0][row] = temp[0];
        state[1][row] = temp[1];
        state[2][row] = temp[2];
        state[3][row] = temp[3];
    }

    return state;
};

/**
* @name         : MixColumns transformation
* @description  : The MixColumns() transformation is a transformation that operates on the State column by column, treating each of them as a 4-term polynomial (view §4.3 for more information)
* @section      : §5.1.3
* @params       : state[Nb][Nb] : State array
                  Nb            : Number of round (view description of cipher function for more information)
* @returns      : state[Nb][Nb] : Output of the State array after shiftRows() transformation
*/
aes.mixColumns = function(state)
{
    for(var col = 0; col < 4; col++)
    {
        var ad, bc, abcd;

        ad = state[col][0] ^ state[col][3];
        bc = state[col][1] ^ state[col][2];

        abcd = ad ^ bc;

        state[col][0] ^= abcd ^ (((state[col][0] ^ state[col][1]) << 1) ^ aes.FFTab[(state[col][0] ^ state[col][1]) >> 7]);
        state[col][1] ^= abcd ^ (((bc) << 1) ^ aes.FFTab[(bc) >> 7]);
        state[col][2] ^= abcd ^ (((state[col][2] ^ state[col][3]) << 1) ^ aes.FFTab[(state[col][2] ^ state[col][3]) >> 7]);
        state[col][3] ^= abcd ^ (((ad) << 1) ^ aes.FFTab[(ad) >> 7]);
    }

    return state;
};

/**
* @name         : SubWord operation
* @description  : Apply S-Box to word
* @section      : §5.2
* @params       : w[Nb] : 4-bytes word
* @returns      : w[Nb] : New 4-bytes word
*/
aes.subWord = function(w)
{
    for(var i = 0; i < 4; i++)
    {
        w[i] = aes.sBox[w[i]];
    }

    return w;
};

/**
* @name         : RotWord operation
* @description  : Rotate word
* @section      : §5.2
* @params       : w[Nb] : 4-bytes word
* @returns      : w[Nb] : New 4-bytes word
*/
aes.rotWord = function(w)
{
    var temp = w[0];

    w[0] = w[1];
    w[1] = w[2];
    w[2] = w[3];
    w[3] = temp;

    return w;
};

/**
* @name         : GetRandomValues operation
* @description  : Generate a random number
* @section      : /
* @params       : length : length of the vector (higher is better)
* @returns      : random number
*/
aes.getRandomValues = function(length)
{
    var randomVector = new Array(length);

    for(var i = 0; i < length; i++)
    {
        randomVector[i] = Math.floor(Math.random() * 0xffff);
    }

    return randomVector[(Math.random() * length)];
};

/**
* @name         : Encrypt using counterMode
* @description  : Encrypt plaintext using counterMode
* @section      : /
* @params       : - plaintext
*                 - password
                  - bits
* @returns      : ciphertext
*/
aes.actions.encrypt = function(plaintext, password, bits)
{
    // Testing key length
    if(bits != 128 && bits != 192 && bits != 256)
    {
        throw new Error("Key size must 128/192/256 bits long");
    }

    // Escape string to have a "secure" one
	plaintext = encodeURIComponent(String(plaintext)).replace(/[!'()*]/g, function(c)
	{
		return '%' + c.charCodeAt(0).toString(16);
	});
	
	password = encodeURIComponent(String(password)).replace(/[!'()*]/g, function(c)
	{
		return '%' + c.charCodeAt(0).toString(16);
	});

    // We'll generate key expansion thanks to plain-password
    var bytes = bits / 8;
    var pBytes = new Array(bytes);

    for(var i = 0; i < bytes; i++)
    {
        pBytes[i] = (i < password.length) ? password.charCodeAt(i) : 0
    }

    // Generate a 16/24/32 bytes long key
    var key = aes.cipher(pBytes, aes.keyExpansion(pBytes));
    key = key.concat(key.slice(0, bytes - 16));

    // Generate CounterBlock
    var counterBlock = new Array(16);

    var nonce = Date.now();
    var nonceM = nonce % 1000;
    var nonceS = Math.floor(nonce / 1000);
    var nonceR = aes.getRandomValues(8);

    for(var i = 0; i < 2; i++)
    {
        counterBlock[i] = (nonceM >>> i * 8) & 0xff;
    }
    
    for(var i = 0; i < 2; i++)
    {
        counterBlock[i + 2] = (nonceR >>> i * 8) &0xff;
    }
    
    for(var i = 0; i < 4; i++)
    {
        counterBlock[i + 4] = (nonceS >>> i * 8) & 0xff;
    }

    // Converting counterBlock to string
    var counterText = "";

    for(var i = 0; i < 8; i++)
    {
        counterText += String.fromCharCode(counterBlock[i]);
    }

    // Generate key schedule
    var keySchedule = aes.keyExpansion(key);

    var countBlock = Math.ceil(plaintext.length / 16);
    var cipherText = "";
    var block = countBlock;

    while(block)
    {
        // Set counter in last 8 bytes of counterBlock
        for(var i = 0; i < 4; i++)
        {
            counterBlock[16 - 1 - i] = (block >>> i * 8) & 0xff;
        }
        
        for(var i = 0; i < 4; i++)
        {
            counterBlock[16 - 1 - i - 4] = (block / 0x100000000 >>> i * 8);
        }

        // Encrypt counterBlock
        var cipherCounter = aes.cipher(counterBlock, keySchedule),
            blockLength = block < countBlock - 1 ? 16 : (plaintext.length - 1)% 16 + 1,
            cipherChar = new Array(blockLength);

        for(var i = 0; i < blockLength; i++)
        {
            cipherChar[i] = String.fromCharCode(cipherCounter[i] ^ plaintext.charCodeAt(block * 16 + i));
        }

        cipherText += cipherChar.join("");

        if(typeof WorkerGlobalScope != 'undefined' && self instanceof WorkerGlobalScope)
        {
            if(block % 1000 == 0)
            {
                self.postMessage({ 
                    action: "encryptFile",
                    progress: (countBlock - block)/countBlock * 100
                });
				
				// console.log((countBlock - block)/countBlock * 100);
            }
        }
        
        block--;
    }

    cipherText = counterText + cipherText;

    // Return the cipherText in base64
    return btoa(cipherText);
};

/**
* @name         : Decrypt using counterMode
* @description  : Decrypt ciphertext using counterMode
* @section      : /
* @params       : - ciphertext
*                 - password
                  - bits
* @returns      : plaintext
*/
aes.actions.decrypt = function(cipherText, password, bits)
{
    // Testing key length
    if(bits != 128 && bits != 192 && bits != 256)
    {
        throw new Error("Key size must 128/192/256 bits long");
    }

    // Decrypting from base64
    cipherText = atob(cipherText);

    // Makes sure that's strings and escape it
    password = encodeURIComponent(String(password)).replace(/[!'()*]/g, function(c)
	{
		return '%' + c.charCodeAt(0).toString(16);
	});

    // We'll generate key expansion thanks to plain password
    var bytes = bits / 8;
    var pBytes = new Array(bytes);

    for(var i = 0; i < bytes; i++)
    {
        pBytes[i] = (i < password.length) ? password.charCodeAt(i) : 0
    }

    // Generate a 16/24/32 bytes long key
    var key = aes.cipher(pBytes, aes.keyExpansion(pBytes));
    key = key.concat(key.slice(0, bytes - 16));

    // Reconvering nonce from first 8 bytes of cipherText
    var counterBlock = new Array(8);
    var counterText = cipherText.slice(0, 8);

    for(var i = 0; i < 8; i++)
    {
        counterBlock[i] = counterText.charCodeAt(i);
    }

    // Generating key schedule
    var keySchedule = aes.keyExpansion(key);

    // Separating cipherText into blocks and skipping first 8 bytes of nonce
    var blockCount = Math.ceil((cipherText.length - 8) / 16);
    var cipher = new Array(blockCount);

    for(var block = 0; block < blockCount; block++)
    {
        cipher[block] = cipherText.slice(8 + block * blockCount, 8 + block * blockCount + 16);
    }

    cipherText = cipher;

    // Plaintext will be show after decryption of cipherText
    var plaintext = "";

    for(var block = 0; block < blockCount; block++)
    {
        for(var i = 0; i < 4; i++)
        {
            counterBlock[16 - 1 - i] = (block >>> i * 8) & 0xff;
            counterBlock[16 - 1 - i - 4] = (((block + 1) / (0x100000000 - 1) >>> i * 8)) & 0xff;
        }

        var cipherCounter = aes.cipher(counterBlock, keySchedule);

        var plainTextByte = new Array(cipherText[block].length);

        for(var i = 0; i < cipherText[block].length; i++)
        {
            plainTextByte[i] = cipherCounter[i] ^ cipherText[block].charCodeAt(i);
            plainTextByte[i] = String.fromCharCode(plainTextByte[i]);
        }

        plaintext += plainTextByte.join("");

        if(typeof WorkerGlobalScope != 'undefined' && self instanceof WorkerGlobalScope)
        {
            if(block % 1000 == 0)
            {
                self.postMessage({ 
                    action: "decryptFile",
                    progress: block/blockCount * 100
                });
            }
        }
    }

    return plaintext;
}
