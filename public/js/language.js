// language.js
var txt; // All strings of user's language
var LANG = 'en'; // User's language

// Default
var ROOT = '/core/';
var VERSION = '';

// Override default variables if it's possible
if(document.querySelector("script#language-js")) {
    var urlpart = document.querySelector("script#language-js").src.split("/public/version/");
    if(urlpart.length === 2) {
        urlpart[0] = urlpart[0].replace(/https?:\/\//i, '');
        urlpart[1] = urlpart[1].split('/').shift();
        if(typeof(urlpart[0]) === 'string') {
            var pos = urlpart[0].indexOf('/');
            ROOT = (pos === -1) ? '/' : urlpart[0].substr(pos)+'/';
        }
        if(typeof(urlpart[1]) === 'string') VERSION = urlpart[1];
    }
}

var IMG = ROOT+'public/pictures/';

$(document).ready(function() {
	var sidebar = $('.sidebar');
	if($(sidebar).length) {
		var current = document.location.href.replace(/https?:\/\//i, '').split(ROOT), link;
		if(current.length > 1) current.shift();
		current = current.join(ROOT).split('?').shift();
		if(current.substr(-1) === '/') current = current.substr(0, current.length - 1);
		if(current.substr(-1) === '#') current = current.substr(0, current.length - 1);

		$(sidebar).find('li > a').removeClass('selected');
		$(sidebar).find('li > a').each(function() { // On load
			link = this.href.replace(this.baseURI, '');
			if(link === current) {
				$(this).addClass('selected'); return false;
			}
		});
		$(sidebar).find('li > a').on('click', function() { // On click
			$(sidebar).find('li > a').removeClass('selected');
			$(this).addClass('selected');
		});
	}
});

function changeLanguage(lang) {
    var date = new Date();
    date.setTime(date.getTime()+(365*24*3600*1000));
    document.cookie = "lang="+lang+"; expires="+date.toGMTString()+"; path=/";
    window.location.reload();
}

function getLanguage() {
    var name = 'lang=';
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
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
    if(typeof(txt) === 'object') return true;
    var clang;
    if(DEFAULT_LANGUAGE) {
        clang = LANG;
    } else {
        clang = getLanguage();
        clan = clang == '' ? LANG : clang;
    }

    var xmlhttp = new XMLHttpRequest();
    var url = ROOT+'public/translations/'+clang+'.json?v='+VERSION;

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
