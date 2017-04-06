/**
** @name        : log_connect.js
** @authors     : Romain Claveau <romain.claveau@protonmail.ch>, Dylan CLEMENT <dylanclement7@protonmail.ch>
** @description : Method to connect the user to the servers
**/


/**
** @name         : sendConnectionRequest()
** @description  : to send username + password + base64encoded encrypted CEK to the server and log in the the user if all is good
**/

window.onload = function() {

    // Get txt from user's language json (language.js)
    getJSON();
	sessionStorage.clear();

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
                console.log("length :: "+xhr.responseText.length);
                if(xhr.responseText.length > 2)
                {
					console.log("ok");
                    // success message
					var rep = xhr.responseText;
					//the responseText have to be: ok@$cek or val@$cek, where $cek is the urlencoded encrypted cek
					var z = rep.split("@");
					console.log(z[0]);
                    if(z[0] == "ok") {
						var cek = z[1];
						try { //we try to decrypt the CEK with the passphrase
							var cek = decodeURIComponent(cek);
							var cek = base64.decode(cek); //the CEK is base64encoded in the database, then we decode it
							var cek = sjcl.decrypt(field_passphrase, cek); //the CEK is now a JSON, we decrypt it
							sessionStorage.setItem("kek", field_passphrase); //we store locally the passphrase
							sessionStorage.setItem("cek", cek); //we store locally the CEK
							window.location.href = ROOT+"Home"; //it's okay, all is good -> redirect the user to the desktop
						} catch (e) { //the passphrase is wrong
							console.log(e.message);
							returnArea.innerHTML = txt.Login.badPassphrase;
						}
						return false;
                    }
                    else if(z[0] == "va") {
						window.location.href = ROOT+"Validate";
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
