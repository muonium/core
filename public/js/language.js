// language.js

var txt; // All strings of user's language
var lang = 'en'; // User's language

var root = '/core/';
var img = root+'public/pictures/';

function changeLanguage(lang)
{
    var date = new Date();
    date.setTime(date.getTime()+(365*24*3600*1000));
    document.cookie = "lang="+lang+"; expires="+date.toGMTString()+"; path=/";
    window.location.reload();
}

function getLanguage() {
    var name = "lang=";
    var ca = document.cookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length,c.length);
        }
    }
    return "";
}

function getJSON() {
    // Get txt from user's language json
    
    var clang = getLanguage();
    if(clang != '') {
        lang = clang;
    }
    
    var xmlhttp = new XMLHttpRequest();
    var url = root+"public/translations/"+lang+".json";

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            setJSON(JSON.parse(xmlhttp.responseText));
        }
    };
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
}

function setJSON(arr) {
    txt = arr;
}