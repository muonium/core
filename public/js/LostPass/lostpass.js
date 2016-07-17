/* lostpass.js */

var changePass = function() {
    var new_pwd = document.querySelector("#pwd").value;
    var pwd_confirm = document.querySelector("#pwd_confirm").value;
    var new_pp = document.querySelector("#pp").value;
    var pp_confirm = document.querySelector("#pp_confirm").value;
    var pass_length = 1;
    
    if(new_pp.length > 0 || new_pwd.length > 0) {
    
        if((new_pwd.length < 6 && new_pwd.length > 0) || (new_pp.length < 6 && new_pp.length > 0))
            pass_length = 0;

        var returnArea = document.querySelector("#returnArea");
        returnArea.innerHTML = "<img src='/core/public/pictures/index/loader.gif' style='height: 3vh;' />";

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "/core/LostPass/resetPass", true);
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
                        window.location.href="Login";
                        return false;
                    }
                    else {
                        // error
                        returnArea.innerHTML = xhr.responseText;
                    }
                }
            }
        }
        
       xhr.send("pwd="+sha512(new_pwd)+"&pwd_confirm="+sha512(pwd_confirm)+"pp="+encodeURIComponent(new_pp)+"&pp_confirm="+encodeURIComponent(pp_confirm)+"&passlength="+pass_length); //xhr.send("old_pwd="+sha512(old_pwd)+"&new_pwd="+sha512(new_pwd)+"&pwd_confirm="+sha512(pwd_confirm)+"&pwd_length="+pwd_length);
    }
}