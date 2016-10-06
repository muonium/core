/*
* @name         : sendRegisterRequest()
* @description  : Permet l'envoi de la requête d'inscription avec les identifiants
*/

/* log_register.js:109 Uncaught ReferenceError: aeJs is not defined
    generateKeys @ log_register.js:109
    xhr.onreadystatechange @ log_register.js:41
*/

if('function' === typeof importScripts) {
    importScripts("aeJs.worker.js");
}

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
                        //generateKeys(field_passphrase);
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

        xhr.send("mail="+field_mail+"&login="+field_login+"&pass="+mui_hash(field_password)+"&pass_confirm="+mui_hash(field_password_confirm)+"&passphrase="+encodeURIComponent(field_passphrase)+"&passphrase_confirm="+encodeURIComponent(field_passphrase_confirm)+"&doubleAuth="+doubleAuth); 
        //xhr.send("mail="+field_mail+"&login="+field_login+"&pass="+mui_hash(field_password)+"&pass_confirm="+mui_hash(field_password_confirm)+"&passphrase="+mui_hash(field_passphrase)+"&passphrase_confirm="+mui_hash(field_passphrase_confirm)+"&doubleAuth="+doubleAuth);

    }
}

/*
* @name         : generateKeys(string mail, string passphrase)
* @description  : Permet la génération de la clé publique et de la clé privée à partir de la passphrase
*/
/*var generateKeys = function(passphrase)
{
    var returnArea = document.querySelector("#return p");
    if(passphrase.length != 0)
    {
        aeJs.actions.encrypt("pp", passphrase);
        //});
        sessionStorage.setItem("passphrase", passphrase);
    }
    else
    {
        returnArea.innerHTML = "Key gen impossible.";
    }
}*/

/*
* @name         : openSession(string passphrase)
* @description  : Permet la génération d'une session ainsi que le stockage en local de la passphrase
*/
/*var openSession = function(passphrase)
{
    var returnArea = document.querySelector("#return p");
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "Inscription/GenerateSession", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function()
    {
        if(xhr.status == 200 && xhr.readyState == 4)
        {
            console.log(xhr.responseText);
            if(xhr.responseText.length == 10) // Longueur du token
            {
                sessionStorage.setItem("t", xhr.responseText);
                sessionStorage.setItem("pp",aeJs.actions.encrypt(passphrase)); //encrypt passphrase in sessionStorage
                //sessionStorage.setItem("pp", CryptoJS.AES.encrypt(passphrase, xhr.responseText));
                returnArea.innerHTML = "End. Redirecting...";
                setTimeout(function(){document.location.href = "Accueil";}, 1000);
            }
            else
            {
                returnArea.innerHTML = "stockage of passphase impossible.";
            }
        }
    }
    xhr.send(null);
}*/
