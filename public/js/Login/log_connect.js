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
					//the responseText have to be: ok@$cek or val@$cek, where $cek is the urlencoded encrypted cek
					var rep = rep.split("@");
                    if(rep[0] == "ok") {
						var cek = rep[1];
						try {
							//we decrypt the CEK which is received from the server
							var cek = decodeURIComponent(cek);
							var cek = base64.decode(cek); //the CEK is base64encoded in the database, then we decode it
							var cek = sjcl.decrypt(field_passphrase, cek); //the CEK is now a JSON, we decrypt it
							var cek = sjcl.codec.hex.fromBits(cek); //we hexa' encode it to store it correctly in sessionStorage (to avoid any compatibility trouble)
							sessionStorage.setItem("kek", field_passphrase); //we store locally the passphrase
							sessionStorage.setItem("cek", cek); //we store locally the CEK
							window.location.href  = "Home"; //it's okay, all is good -> redirect the user to the desktop
						} catch (e) {
							//if the cek decryption didn't work
							console.log(e.message);
							returnArea.innerHTML = "<p>Error : bad passphrase.</p>"; // TODO: put this string in JSON files
						}
						return false;
                    }
                    else if(rep[0] == "va") {
						// TODO: cek decryption at the Validate view page, for Dylan
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
