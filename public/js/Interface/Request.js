var QuantaCloud = 
{
    clickEvent: function(element, action, controller)
    {
        switch(element.id)
        {
            /*
            * Header
            */
            case "header_button_bug":
                break;
                
            case "header_button_help":
                break;
                
            case "header_button_settings":
                break;
                
            case "header_button_user":
                break;
                
                
            /*
            * Toolbar
            */
            case "toolbar_button_recents":
                xhr = new XMLHttpRequest();
                xhr.open("GET",controller+"/"+action, true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function()
                {
                    if(xhr.status == 200 && xhr.readyState == 4)
                    {
                        document.body.innerHTML = xhr.responseText;
                        document.title = "Recents";
                        document.querySelector("img.arrow").className = "arrow recents";
                    }
                }
                xhr.send(null);
                break;
            case "toolbar_button_favorite":
            	xhr = new XMLHttpRequest();
                xhr.open("GET",controller+"/"+action, true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function()
                {
                    if(xhr.status == 200 && xhr.readyState == 4)
                    {   
                        document.title = "Favoris";
                        document.body.innerHTML = xhr.responseText;
                        document.querySelector("img.arrow").className = "arrow favoris";
                    }
                }
                xhr.send(null);
                break;
            case "toolbar_button_general":
                xhr = new XMLHttpRequest();
                xhr.open("GET",controller+"/"+action, true);
                xhr.setRequestHeader("Content-type", "text/html");
                xhr.onreadystatechange = function()
                {
                    if(xhr.status == 200 && xhr.readyState == 4)
                    {
                        document.body.innerHTML = xhr.responseText;
                        document.title = "General";
                        document.querySelector("img.arrow").className = "arrow general";
                    }
                }
                xhr.send(null);
            	break;
            case "toolbar_button_share":
                xhr = new XMLHttpRequest();
                xhr.open("GET",controller+"/"+action, true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function()
                {
                    if(xhr.status == 200 && xhr.readyState == 4)
                    {
                        document.body.innerHTML = xhr.responseText;
                        document.title = "Partage";
                        document.querySelector("img.arrow").className = "arrow share";
                    }
                } 
                xhr.send(null);
            	break; 
            case "toolbar_button_transfers":
                xhr = new XMLHttpRequest();
                xhr.open("GET",controller+"/"+action, true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function()
                {
                    if(xhr.status == 200 && xhr.readyState == 4)
                    {
                        document.body.innerHTML = xhr.responseText;
                        document.title = "Transferts";
                        document.querySelector("img.arrow").className = "arrow transfers";
                    }
                }
                xhr.send(null);
            	break;
                
            /*
            * Default
            */
                default :
                QC.init();
                break;
        }
    }
} || {};
