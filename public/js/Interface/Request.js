//var pageLoaded = '';
// modulesLoaded : Modules in interface.js
var Request =
{
    modulesLoaded: false,
    load: function(controller, action)
    {
        if(action === 'DefaultAction' || action === undefined)
            var url = controller;
        else
            var url = controller+'/'+action;
        console.log("Loading "+url);

        if(url.length > 0) {
            // Load page
            xhr = new XMLHttpRequest();
            xhr.open("GET", url, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function()
            {
                if(xhr.status == 200 && xhr.readyState == 4)
                {
                    //pageLoaded = '';
                    history.pushState({ path: this.path }, '', url);
                    document.documentElement.innerHTML = xhr.responseText;

                    switch(url) {
                        case "User":
                            if(Request.modulesLoaded)
                                if(Trash.State === 1)
                                    document.querySelector("#button_trash").innerHTML = txt.User.trash_1;
                            // Call loader from interface.js to load "User" page features
                            UserLoader(Folders.id);
                            break;

                        case "Favorites":

                            break;
                    }
                }
            }
            xhr.send(null);
        }
    }
} || {};
