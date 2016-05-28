/*
* @name         : sendRegisterRequest()
* @description  : Permet l'envoi de la requête d'inscription avec les identifiants
*/
var sendRegisterRequest = function()
{
    console.log("Début inscription");
    
    var field_mail = document.querySelector("#field_mail").value;
    var field_pseudo = document.querySelector("#field_mail").value;
    var field_password = sha512(document.querySelector("#field_pass").value);
    var field_password_confirm  = sha512(document.querySelector("#field_pass_confirm").value);
    var field_passphrase = document.querySelector("#field_passphrase").value;
    var field_passphrase_confirm = document.querySelector("#field_passphrase_confirm").value;
    
    var returnArea = document.querySelector("#return p");
    
    returnArea.innerHTML = "<img src='./public/pictures/index/loader.gif' style='height: 3vh;' />";
    
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "Inscription/addUser", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
    xhr.onreadystatechange = function()
    {
        if(xhr.status == 200 && xhr.readyState == 4)
        {
            console.log(xhr.responseText);
            switch(xhr.responseText)
            {
                case "ok":                                
                    generateKeys(field_mail, field_passphrase);
                    break;
                
                case "mailExists":
                    returnArea.innerHTML = "Votre mail est déjà enregistré dans la base de données. Impossible de recréer un compte à partir de celle-ci.";
                    break;
                    
                case "badPassConfirm":
                    returnArea.innerHTML = "Les deux mots de passe sont différents.";
                    break;
                    
                case "badPassphraseConfirm":
                    returnArea.innerHTML = "Les deux passphrase sont différentes.";
                    break;
                    
                case "passEqualPassphrase":
                    returnArea.innerHTML = "Le mot de passe et la passphrase doivent être différents.";
                    break;
                    
                case "form":
                    returnArea.innerHTML = "Tous les champs doivent être remplis.";
                    break;
                    
                default:
                    break;
            }
        }
    }
    xhr.send("mail="+field_mail+"&pseudo="+field_pseudo+"&pass="+field_password+"&pass_confirm="+field_password_confirm+"&passphrase="+sha512(field_passphrase)+"&passphrase_confirm="+sha512(field_passphrase_confirm));
}

/*
* @name         : generateKeys(string mail, string passphrase)
* @description  : Permet la génération de la clé publique et de la clé privée à partir de la passphrase
*/
var generateKeys = function(mail, passphrase)
{
    var returnArea = document.querySelector("#return p");
    
    if(mail.length != 0 && passphrase.length != 0)
    {
       
        var options = 
        {
            numBits: 4096,
            userId: mail,
            passphrase: passphrase
        };
        openpgp.generateKeyPair(options).then(function(keypair) 
        {
            alert("test");
            var privkey = keypair.privateKeyArmored;
            var pubkey = keypair.publicKeyArmored;
            
            //console.log(privkey);
           // console.log(pubkey);
            
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "Inscription/uploadKeys", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function()
            {
                if(xhr.status == 200 && xhr.readyState == 4)
                {
                    console.log(xhr.responseText);
                    switch(xhr.responseText)
                    {
                        case "ok":                            
                            sessionStorage.setItem("PK_1", privkey);
                            sessionStorage.setItem("PK_2", pubkey);
                            
                            openSession(passphrase);
                            break;
                            
                        case "error":
                            returnArea.innerHTML = "Une erreur s'est produite lors de l'upload des clés.";
                            break;
                            
                        default:
                            break;
                    }
                }
            }
            
            xhr.send("privateKey="+encodeURIComponent(privkey)+"&publicKey="+encodeURIComponent(pubkey));
            
        }).catch(function(error) 
        {
            returnArea.innerHTML = "Impossible de générer les clés.";
        });
    }
    else
    {
        returnArea.innerHTML = "Impossible de générer les clés.";
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
                sessionStorage.setItem("pp", CryptoJS.AES.encrypt(passphrase, xhr.responseText));
                
                returnArea.innerHTML = "Inscription terminée. Redirection en cours...";
                
                setTimeout(function(){document.location.href = "Accueil";}, 1000);
            }
            else
            {
                returnArea.innerHTML = "Impossible de stocker la passphrase";
            }
        }
    }
    
    xhr.send(null);
}