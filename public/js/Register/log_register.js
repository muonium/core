/*
@dependencies: cek_generation.js
@description: to send the username+password+generate a cek for the user
*/

window.onload = function() {

    // Get txt from user's language json (language.js)
    getJSON();

    window.addEventListener("keydown", function(event) {
        switch(event.keyCode) {
            case 13:
                // enter
                sendRegisterRequest();
                break;
        }
    });
}

/*
* @name         : enc
* @description: encrypt and next base64 encode it to store it in the database
*/
var enc = function(key, passphrase){
	var a = sjcl.random.randomWords(1);
	var i = sjcl.random.randomWords(4);
	var s = sjcl.random.randomWords(2);
	//encrypt it
	var key = sjcl.encrypt(passphrase, key, {mode:'gcm', iv:i, salt:s, iter:2000, ks:256, adata:a, ts:128});
	var key = base64.encode(key); //don't store a Json in mongoDB...
	return key;
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

    if(field_mail.length < 6 || field_login.length < 2)
        returnArea.innerHTML = txt.Register.form;
    else if(field_password.length < 6 || field_password_confirm.length < 6 || field_passphrase.length < 6 || field_passphrase_confirm.length < 6)
        returnArea.innerHTML = txt.Register.passLength;
    else {

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
                        window.location.href="Home";
                        return false;
                    }
                    else {
                        // error
                        returnArea.innerHTML = xhr.responseText;
                    }
                }
            }
        }

		var cek_plt = sjcl.random.randomWords(4); //we generate a new CEK (plt = plaintext)
		var cek_xhr = enc(cek_plt, field_passphrase); //encryption of the CEK under the KEK (alias "passphrase") and b64encoding (cf. public/src/crypto/gen_cek.js)
        xhr.send("mail="+field_mail+"&login="+field_login+"&pass="+mui_hash(field_password)+"&pass_confirm="+mui_hash(field_password_confirm)+"&doubleAuth="+doubleAuth+"&cek="+encodeURIComponent(cek_xhr));
    }
}
