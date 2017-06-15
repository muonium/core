var Favorites = (function() {
    return {
        update : function(id, status)
		{
            Box.hide();
			
			console.log(id);
			console.log(status);
			
            if(isNumeric(id) && isNumeric(status))
			{
				var xhr = new XMLHttpRequest();
				xhr.open("POST", "User/Favorites", true);
				xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

				xhr.onreadystatechange = function()
				{
					if(xhr.status == 200 && xhr.readyState == 4)
					{
						console.log(xhr.responseText);
					}
				}
				xhr.send("id=" + id + "&status=" + status);
            }
        }
    }
});
