// language.js
var txt; // All strings of user's language
var LANG = 'en'; // User's language

// Default
var ROOT = '/core/';
var VERSION = '';

// Override default variables if it's possible
if(document.querySelector("script#language-js")) {
    var urlpart = document.querySelector("script#language-js").src.split("/public/js");
    if(urlpart.length == 2) {
        urlpart[0] = urlpart[0].replace(/https?:\/\//i, '');
        urlpart[1] = urlpart[1].replace('/language.js?v=', '');
        if(typeof(urlpart[0]) === 'string') {
            var pos = urlpart[0].indexOf('/');
            if(pos === -1) ROOT = '/';
            else ROOT = urlpart[0].substr(pos)+'/';
        }
        if(typeof(urlpart[1]) === 'string') VERSION = urlpart[1];
    }
}

var IMG = ROOT+'public/pictures/';
//console.log(ROOT);
//console.log(VERSION);

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

function getJSON(DEFAULT_LANGUAGE = false) {
    // Get txt from user's language json
    var clang;
    if(DEFAULT_LANGUAGE) {
        clang = LANG;
    }
    else {
        clang = getLanguage();
        if(clang == '') {
            clang = LANG;
        }
    }

    var xmlhttp = new XMLHttpRequest();
    var url = ROOT+"public/translations/"+clang+".json?v="+VERSION;

    xmlhttp.onreadystatechange = function() {
        if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            try {
                var json = JSON.parse(xmlhttp.responseText);
            } catch(e) {
                console.log("Errors found in "+clang+".json, loading "+LANG+".json");
                if(clang != LANG) {
                    getJSON(true);
                }
                else {
                    window.location.href=ROOT+"Error/404";
                }
            }
            setJSON(json);
            LANG = clang;
        }
    };
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
}

function setJSON(arr) {
    txt = arr;
}
