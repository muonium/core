function changeLanguage(lang)
{
    var date = new Date();
    date.setTime(date.getTime()+(365*24*3600*1000));
    document.cookie = "lang="+lang+"; expires="+date.toGMTString()+"; path=/";
    window.location.reload();
}