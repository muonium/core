/* profile.js */

var changeLogin = function() {
    var login = document.querySelector("#login").value;

    var returnArea = document.querySelector("#changeLoginReturn");
    returnArea.innerHTML = "<img src='./public/pictures/index/loader.gif' style='height: 3vh;' />";

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "Profile/changeLogin", true);
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
    var old_pwd = document.querySelector("#oldpwd").value;
    var new_pwd = document.querySelector("#newpwd").value;
    var pwd_confirm = document.querySelector("#pwdconfirm").value;
    var pwd_length = 1;

    if(new_pwd.length < 6)
        pwd_length = 0;

    var returnArea = document.querySelector("#changePasswordReturn");
    returnArea.innerHTML = "<img src='./public/pictures/index/loader.gif' style='height: 3vh;' />";

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "Profile/changePassword", true);
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
    xhr.send("old_pwd="+sha512(old_pwd)+"&new_pwd="+sha512(new_pwd)+"&pwd_confirm="+sha512(pwd_confirm)+"&pwd_length="+pwd_length);
}

var changePassPhrase = function() {
    var old_pp = document.querySelector("#oldpp").value;
    var new_pp = document.querySelector("#newpp").value;
    var pp_confirm = document.querySelector("#ppconfirm").value;
    var pp_length = 1;

    if(new_pp.length < 6)
        pp_length = 0;

    var returnArea = document.querySelector("#changePassPhraseReturn");
    returnArea.innerHTML = "<img src='./public/pictures/index/loader.gif' style='height: 3vh;' />";

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "Profile/changePassPhrase", true);
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
    xhr.send("old_pp="+old_pp+"&new_pp="+new_pp+"&pp_confirm="+pp_confirm+"&pp_length="+pp_length);
}
