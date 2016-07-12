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

var sendRegisterRequest = function()
{
    console.log("Start register");

    var field_mail = document.querySelector("#field_mail").value;
    var field_pseudo = document.querySelector("#field_pseudo").value;
    var field_password = document.querySelector("#field_pass").value;
    var field_password_confirm  = document.querySelector("#field_pass_confirm").value;
    var field_passphrase = document.querySelector("#field_passphrase").value;
    var field_passphrase_confirm = document.querySelector("#field_passphrase_confirm").value;

    var passLength = 1;

    var returnArea = document.querySelector("#return p");

    returnArea.innerHTML = "<img src='./public/pictures/index/loader.gif' style='height: 3vh;' />";

    if(field_password.length < 6 || field_password_confirm.length < 6 || field_passphrase.length < 6 || field_passphrase_confirm.length < 6) {
        passLength = 0;
    }

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "Register/addUser", true);
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
    xhr.send("mail="+field_mail+"&pseudo="+field_pseudo+"&pass="+sha512(field_password)+"&pass_confirm="+sha512(field_password_confirm)+"&passphrase="+field_passphrase+"&passphrase_confirm="+field_passphrase_confirm+"&passlength="+passLength);

}

/*
* @name         : generateKeys(string mail, string passphrase)
* @description  : Permet la génération de la clé publique et de la clé privée à partir de la passphrase
*/
var generateKeys = function(passphrase)
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
}

/*
* @name         : openSession(string passphrase)
* @description  : Permet la génération d'une session ainsi que le stockage en local de la passphrase
*/
var openSession = function(passphrase)
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
}
