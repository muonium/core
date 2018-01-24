/* lostpass.js */

window.onload = function() {

    // Get txt from user's language json (language.js)
    getJSON();

    window.addEventListener("keydown", function(event) {
        switch(event.keyCode) {
            case 13:
                // enter
                changePass();
                break;
        }
    });
}

var changePass = function() {
    var new_pwd = document.querySelector("#pwd").value;
    var pwd_confirm = document.querySelector("#pwd_confirm").value;
    //var new_pp = document.querySelector("#pp").value;
    //var pp_confirm = document.querySelector("#pp_confirm").value;
    var new_pp = '', pp_confirm = '';

    var returnArea = document.querySelector("#returnArea");

    if(new_pp.length > 0 || new_pwd.length > 0) {

        if((new_pwd.length < 6 && new_pp.length < 6) || (new_pp.length < 6 && new_pp.length > 0) || (new_pwd.length < 6 && new_pwd.length > 0)) {
            returnArea.innerHTML = txt.Register.passLength;
            return false;
        }

        returnArea.innerHTML = "";

        if(new_pwd.length >= 6) {
            if(pwd_confirm != new_pwd) {
                returnArea.innerHTML = txt.Register.badPassConfirm;
                return false;
            }
            new_pwd = mui_hash(new_pwd);
        }
        else
            new_pwd = '';

        if(new_pp.length >= 6) {
            if(pp_confirm != new_pp) {
                returnArea.innerHTML = txt.Register.badPassphraseConfirm;
                return false;
            }
            //new_pp = mui_hash(new_pp);
            new_pp = encodeURIComponent(new_pp);
        }
        else
            new_pp = '';

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "LostPass/ResetPass", true);
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
                        window.location.href=ROOT+"Login";
                        return false;
                    }
                    else {
                        // error
                        returnArea.innerHTML = xhr.responseText;
                    }
                }
            }
        }

        xhr.send("pwd="+new_pwd+"&pp="+new_pp);
    }
}
