// Files module. Loaded in window.onload()
var Files = (function() {
	// Private

	// Public
	return {
		dl : function(id) {
			Box.hide();
			var f = document.querySelector("#"+id);
			if(f) {
				if(f.getAttribute("data-title").length > 0 && f.getAttribute("data-folder").length > 0) {
					Decryption.getNbChunks(f.getAttribute("data-title"), f.getAttribute("data-folder"));
				}
			}
		},

        details : function(el) {
            var elem;
            if(elem = document.querySelector("#"+el)) {
                Box.box_more = true;
                Box.reset();
                Box.Area = 1;
                Box.set("<p style='padding:5px'>\
                <button onclick=\"Box.right_click(event.clientX, event.clientY, '"+el+"')\"><</button> &nbsp;&nbsp;<strong>Details</strong>\
                <hr><ul><li>"+txt.User.name+" : "+elem.getAttribute("data-title")+"</li>\
                <li>"+txt.User.path+" : "+elem.getAttribute("data-path")+"/</li>\
                <li>"+txt.User.type+" : "+txt.User.file+" <span class='ext_icon'></span></li>\
                <li>"+txt.User.size+" : "+elem.innerHTML.substr(elem.innerHTML.lastIndexOf("["))+"</li>\
                <li>"+elem.title+"</li></ul></p>");

                var newNode = document.importNode(elem.getElementsByTagName('img')[0], true);
                document.querySelector(".ext_icon").appendChild(newNode);
                Box.show();
            }
        }
	}
});
