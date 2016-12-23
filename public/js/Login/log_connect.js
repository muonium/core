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
					var rep = xhr.responseText;
					var rep = rep.split("@");
                    if(rep[0] == "ok") {
						var cek = rep[1];
						try {
							var cek = decodeURIComponent(cek);
							var cek = base64.decode(cek);
							var cek = sjcl.decrypt(field_passphrase, cek);
							sessionStorage.setItem("kek", field_passphrase);
							sessionStorage.setItem("cek", cek)
							window.location.href  = "Home";
						} catch (e) {
							console.log(e.message);
							returnArea.innerHTML = "<p>Error : bad passphrase.</p>";
						}
						return false;
                    }
                    else if(rep[0] == "va") {
						var cek = rep[1];
						try {
							var cek = decodeURIComponent(cek);
							var cek = base64.decode(cek);
							var cek = sjcl.decrypt(field_passphrase, cek);
							sessionStorage.setItem("kek", field_passphrase);
							sessionStorage.setItem("cek", cek);
							window.location.href = "Validate";
						} catch (e) {
							console.log(e.message);
							returnArea.innerHTML = "<p>Error : bad passphrase</p>";
						}
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
