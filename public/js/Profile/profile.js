/* profile.js */

window.onload = function() {

    // Get txt from user's language json (language.js)
    getJSON();
}

var changeLogin = function() {
    var login = document.querySelector("#login").value;

    var returnArea = document.querySelector("#changeLoginReturn");
    returnArea.innerHTML = "<img src='./public/pictures/index/loader.gif' style='height: 3vh;' />";

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "Profile/ChangeLogin", true);
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
                    window.location.href="Profile";
                    return false;
                }
                else {
                    // error
                    returnArea.innerHTML = xhr.responseText;
                }
            }
        }
    }
    xhr.send("login="+encodeURIComponent(login));
}

var changePassword = function() {
    var returnArea = document.querySelector("#changePasswordReturn");
    returnArea.innerHTML = "<img src='./public/pictures/index/loader.gif' style='height: 3vh;' />";

    var old_pwd = document.querySelector("#oldpwd").value;
    var new_pwd = document.querySelector("#newpwd").value;
    var pwd_confirm = document.querySelector("#pwdconfirm").value;

    if(new_pwd.length < 6)
        returnArea.innerHTML = txt.Register.form;
    else {

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "Profile/ChangePassword", true);
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
                        window.location.href="Profile";
                        return false;
                    }
                    else {
                        // error
                        returnArea.innerHTML = xhr.responseText;
                    }
                }
            }
        }
        xhr.send("old_pwd="+sha512(old_pwd)+"&new_pwd="+sha512(new_pwd)+"&pwd_confirm="+sha512(pwd_confirm));
    }
}

var changePassPhrase = function() {
    var returnArea = document.querySelector("#changePassPhraseReturn");
    returnArea.innerHTML = "<img src='./public/pictures/index/loader.gif' style='height: 3vh;' />";

    var old_pp = document.querySelector("#oldpp").value;
    var new_pp = document.querySelector("#newpp").value;
    var pp_confirm = document.querySelector("#ppconfirm").value;

    if(new_pp.length < 6)
        returnArea.innerHTML = txt.Register.form;
    else {

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "Profile/ChangePassPhrase", true);
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
                        window.location.href="Profile";
                        return false;
                    }
                    else {
                        // error
                        returnArea.innerHTML = xhr.responseText;
                    }
                }
            }
        }
        xhr.send("old_pp="+encodeURIComponent(old_pp)+"&new_pp="+encodeURIComponent(new_pp)+"&pp_confirm="+encodeURIComponent(pp_confirm));
        //xhr.send("old_pp="+sha512(old_pp)+"&new_pp="+sha512(new_pp)+"&pp_confirm="+sha512(pp_confirm));
    }
}

var changeAuth = function() {
    var returnArea = document.querySelector("#changeAuthReturn");
    var doubleAuth = document.querySelector("#doubleAuth").checked;
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "Profile/changeAuth", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function()
    {
        if(xhr.status == 200 && xhr.readyState == 4) {
            console.log(xhr.responseText);
            returnArea.innerHTML = xhr.responseText;
        }
    }
    xhr.send("doubleAuth="+doubleAuth);
}

//change user email

var changeMail = function() {
    var changemail = document.querySelector("#changemail").value;

    var returnArea = document.querySelector("#changeMailReturn");
    returnArea.innerHTML = "<img src='./public/pictures/index/loader.gif' style='height: 3vh;' />";

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "Profile/ChangeMail", true);
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
                    window.location.href="Profile";
                    return false;
                }
                else {
                    // error
                    returnArea.innerHTML = xhr.responseText;
                }
            }
        }
    }
    xhr.send("changemail="+encodeURIComponent(changemail));
}
