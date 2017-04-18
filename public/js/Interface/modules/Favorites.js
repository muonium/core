var Favorites = (function() {
    return {
        update : function(fav) {
            Box.hide();
            /*if(fav.length > 1) {
                var id = fav.substr(1);
                if(isNumeric(id)) {
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "User/Favorites", true);
                    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

                    xhr.onreadystatechange = function()
                    {
                        if(xhr.status == 200 && xhr.readyState == 4)
                        {
                            //
                        }
                    }
                    xhr.send("id="+id);
                }
            }*/
        }
    }
});
