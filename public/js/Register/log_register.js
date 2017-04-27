/*
@dependencies: cek_generation.js
@description: to send the username+password+generate a cek for the user
*/

window.onload = function() {

    // Get txt from user's language json (language.js)
    getJSON();

	//for the private beta
	alert("Notice:\
	- The private beta is just here to test and find some bugs left\n\
	- We can delete all the users data whenever we need\n\
	- The encryption key is the same for all the users, so you don't have a privacy yet\n\
	- Don't put any personal documents for the moment\n\
	Servers are in France for now. We didn't create the Estonian company yet.\n\
	Once it's done, users will be able to have their own private key. ");

    window.addEventListener("keydown", function(event) {
        switch(event.keyCode) {
            case 13:
                // enter
                sendRegisterRequest();
                break;
        }
    });
}


//Thanks to Nimphious
//Code found on stackoverflow (sometimes it's good to be lazy)
var randomString = function (length, chars) {
    var mask = '';
    if (chars.indexOf('a') > -1) mask += 'abcdefghijklmn!@#$%^&*()_+-={}[]";\'<>?,opqrstuvwxyz';
    if (chars.indexOf('A') > -1) mask += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    if (chars.indexOf('#') > -1) mask += '0123456789';
    if (chars.indexOf('!') > -1) mask += '~`!@#$%^&*()_+-={}[]";\'<>?,';
    var result = '';
    for (var i = length; i > 0; --i) result += mask[Math.floor(Math.random() * mask.length)];
    return result;
}
/**
** @name         :  cek
** @description: generate & encrypt cek
** y = passphrase
**/
var cek = {};
cek.encrypt = function(key, y){
	//crypto parameters
	var a = sjcl.random.randomWords(4); //authentication data - 128 bits
	var i = sjcl.random.randomWords(4); //initialization vector - 128 bits
	var s = sjcl.random.randomWords(4); //salt - 256 bits
	//encrypt it
	var key = sjcl.encrypt(y, key, {mode:'gcm', iv:i, salt:s, iter:7000, ks:256, adata:a, ts:128});
	var key = base64.encode(key); //don't store a Json in mongoDB...
	return key;
}
cek.gen = function(y){
	var t = randomString(32, '#A!');
	return cek.encrypt(t, y); //encrypt it
}

/*
* @name         : sendRegisterRequest()
* @description  : send the user's informations
*/
var sendRegisterRequest = function()
{
    console.log("Start register");

    var field_mail = document.querySelector("#field_mail").value;
    var field_login = document.querySelector("#field_login").value;
    var field_password = document.querySelector("#field_pass").value;
    var field_password_confirm  = document.querySelector("#field_pass_confirm").value;
    var field_passphrase = document.querySelector("#field_passphrase").value;
    var field_passphrase_confirm = document.querySelector("#field_passphrase_confirm").value;
    var doubleAuth = document.querySelector("#doubleAuth").checked;

    var returnArea = document.querySelector("#return p");

    returnArea.innerHTML = "<img src='./public/pictures/index/loader.gif' style='height: 3vh;' />";

    if(field_mail.length < 6 || field_login.length < 2){
        returnArea.innerHTML = txt.Register.form;
    }else if(field_password.length < 6 || field_password_confirm.length < 6 || field_passphrase.length < 6 || field_passphrase_confirm.length < 6){
        returnArea.innerHTML = txt.Register.passLength;
	}else if (field_password == field_passphrase) {
		returnArea.innerHTML = txt.Register.passEqualPassphrase;
	}else {

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "Register/AddUser", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function()
        {
            if(xhr.status == 200 && xhr.readyState == 4)
            {
                console.log(xhr.responseText);
                if(xhr.responseText.length > 2)
                {
                    // success message
                    if(xhr.responseText.substr(0, 3) == "ok@") {
                        returnArea.innerHTML = xhr.responseText.substr(3);
                        window.location.href=ROOT+"Home";
                        return false;
                    }
                    else {
                        // error
                        returnArea.innerHTML = xhr.responseText;
                    }
                }
            }
        }

		var cek_xhr = cek.gen(field_passphrase); //encryption of the CEK under the KEK (alias "passphrase") and b64encoding
        xhr.send("mail="+field_mail+"&login="+field_login+"&pass="+mui_hash(field_password)+"&pass_confirm="+mui_hash(field_password_confirm)+"&doubleAuth="+doubleAuth+"&cek="+encodeURIComponent(cek_xhr));
    }
}
