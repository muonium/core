// Files module. Loaded in window.onload()
var Files = (function() {
	// Private
	var f_dec = [];
	var i = 0;

	// Public
	return {
		style : 'list',

		dl : function(id) {
			var dwl, btn, spn;
			Box.hide();
			var f = document.querySelector("#"+id);
			if(f) {
				if(f.getAttribute("data-title").length > 0 && f.getAttribute("data-folder").length > 0) {
					dwl = document.createElement('div');
					dwl.id = 'div_download'+i;

					btn = document.createElement('i');
					btn.setAttribute('data-id', i);
					btn.onclick = Files.abort;
					btn.className = 'fa fa-minus-circle btn-abort';
					btn.setAttribute('aria-hidden', true);

					spn = document.createElement('span');
					spn.id = 'span_download'+i;

					dwl.appendChild(btn);
					dwl.appendChild(spn);
					document.querySelector("#transfers_download").appendChild(dwl);
					Transfers.open();
					Transfers.showDl();
					f_dec[i] = new Decryption(f.getAttribute("data-title"), f.getAttribute("data-folder"), i);
					i++;
				}
			}
		},

		abort : function() {
			var j = this.getAttribute('data-id');
			console.log("Aborting "+j);
			f_dec[j].abort();
        },

        details : function(el) {
            var elem;
            if(elem = document.querySelector("#"+el)) {
				var title = elem.getAttribute("title").split("\n");
                Box.box_more = true;
                Box.reset();
                Box.Area = 1;
                Box.set("<p style='padding:5px'>\
                <button onclick=\"Box.right_click(event.clientX, event.clientY, '"+el+"')\"><</button> &nbsp;&nbsp;<strong>"+txt.User.details+"</strong>\
                <hr><ul><li>"+txt.User.name+" : "+elem.getAttribute("data-title")+"</li>\
                <li>"+txt.User.path+" : "+elem.getAttribute("data-path")+"/</li>\
                <li>"+txt.User.type+" : "+txt.User.file+" <span class='ext_icon'></span></li>\
                <li>"+txt.User.size+" : "+title[0]+"</li>\
                <li>"+title[1]+"</li></ul></p>");

                var newNode = document.importNode(elem.getElementsByTagName('img')[0], true);
                document.querySelector(".ext_icon").appendChild(newNode);
                Box.show();
            }
        },

		display : function() {
			if(Files.style == 'mosaic') {
				document.querySelector("#tree").className = 'mosaic';
			}
			else {
				document.querySelector("#tree").className = '';
			}
		}
	}
});
