/**
** @name        : log_connect.js
** @authors     : Romain Claveau <romain.claveau@protonmail.ch>, Dylan CLEMENT <dylanclement7@protonmail.ch>
** @description : Méthode permettant la connexion à l'application
**/


/*
* @name         : sendConnectionRequest()
* @description  : Permet l'envoi de la requête de connexion avec les identifiants
*/

window.onload = function() {

    // Get txt from user's language json (language.js)
    getJSON();

    window.addEventListener("keydown", function(event) {
        switch(event.keyCode) {
            case 13:
                // enter
                sendConnectionRequest();
                break;
        }
    });
}

var sendConnectionRequest = function()
{
    var field_username = document.querySelector("#field_username").value;
    var field_password = document.querySelector("#field_password").value;
    var field_passphrase = document.querySelector("#field_passphrase").value;

    var returnArea = document.querySelector("#return");

    returnArea.innerHTML = "<img src='./public/pictures/index/loader.gif' style='height: 3vh;' />";

    if(field_password.length < 6 || field_passphrase.length < 1 || field_username.length < 3)
        returnArea.innerHTML = txt.Register.form;
    else {

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "Login/Connection", true);
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
						decryptCek(field_passphrase, field_username);
						return false;
                    }
                    else if(xhr.responseText.substr(0, 3) == "va@") {
						window.location.href = "Validate";
                        return false;
                    }
                    else {
                        // error
                        returnArea.innerHTML = xhr.responseText;
                    }
                }
            }
        }

       xhr.send("username="+encodeURIComponent(field_username)+"&pass="+mui_hash(field_password));
    }
}

//requires sjcl.js and base64.js
var decryptCek = function(kek, usr){
	var xhr = new XMLHttpRequest();
	xhr.open("POST", "Login/GetCek", true)
	xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

	xhr.onreadystatechange = function(){
		if (xhr.status == 200 && xhr.readyState == 4) {
			var cek = xhr.responseText;
		}
	}
	xhr.send("username="+encodeURIComponent(usr));

	var cek = base64.decode(cek);
	testCek(kek, cek);
	sessionStorage.setItem("kek", kek);
	window.location.href="Home";
}

var testCek = function(kek, cek){
	try {
		var cek = sjcl.decrypt(kek, cek);
		sessionStorage.setItem("cek", cek);
	} catch (e) {
		returnArea.innerHTML = "<p>Your passphrase is invalid!</p>";
	}
}
