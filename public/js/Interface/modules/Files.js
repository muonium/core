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
					if($('#transfers_download > div').length === 0) {
						$('#transfers_download').html(' ');
					}
					document.querySelector("#transfers_download").appendChild(dwl);
					Transfers.open();
					Transfers.showDl();
                    console.log(f.getAttribute("data-title"));
                    console.log(f.getAttribute("data-folder"));
					f_dec[i] = new Decryption(f.getAttribute("data-title"), f.getAttribute("data-folder"), i);
					i++;
				}
			}
		},

		share : function(id, passphrase) {
			Box.hide();
			var cek = sessionStorage.getItem("cek");
			if (cek == null) { //check if the cek is there
				sessionStorage.clear();
				window.location.href = ROOT+"Logout"; //doesn't exist ? Then logout the user
				return false;
			}
			var filename = Files.getNameById(id);
			if(filename === false || typeof(passphrase) !== 'string') return false;// || passphrase.length < 6) return false;
			// Get first chunk
			$.post('User/getChunk', {line: 0, filename: filename, folder_id: Folders.id}, function(chk) {
				chk = chk.split(':'); //ciphered_chk, salt, authentification data, initialization vector
				if(chk.length === 4 && typeof(chk) === 'object') {
					var f_salt = chk[1];
					var fek = sjcl.misc.pbkdf2(cek, f_salt, 7000, 256); // Key derivation

					var salt = sjcl.random.randomWords(4);
 					var iv = sjcl.random.randomWords(4);
 					var aDATA = sjcl.random.randomWords(4);
					//Password derivation to get dk
 					var dk = sjcl.misc.pbkdf2(passphrase, salt, 7000, 256);
					var enc = new sjcl.cipher.aes(dk);
					// Ciphering `fek`
 					var enc_fek = sjcl.mode.gcm.encrypt(enc, fek, iv, aDATA, 128);
					// Package
					var packet = sjcl.codec.base64.fromBits(enc_fek)+":"+sjcl.codec.base64.fromBits(salt)+":"+sjcl.codec.base64.fromBits(aDATA)+":"+sjcl.codec.base64.fromBits(iv);
					$.post('User/shareFile', {id: id, dk: packet}, function(resp) {
						$('#f'+id).data('shared', '1');
						if(resp.trim() !== 'error') {
							var m = new MessageBox(txt.User.shared).setSize('35%','auto').addInput('url', {
								autocomplete: "off",
								value: resp,
								style: 'width:100%'
							}).addButton(txt.RightClick.copy, function() {
								this.$inputs.url.select();
								document.execCommand('copy');
							}).show();
						}
					});
				}
			});
		},

		unshare : function(id) {
			Box.hide();
			$.post('User/unshareFile', {id: id}, function(resp) {
				$('#f'+id).data('shared', '0');
			});
		},

		abort : function() {
			var j = this.getAttribute('data-id');
			console.log("Aborting "+j);
			f_dec[j].abort();
        },

		getNameById : function(id) {
			if($('#f'+id).length > 0 && $('#f'+id).data('title') !== undefined && $('#f'+id).data('title') !== null) {
				return $('#f'+id).data('title');
			}
			return false;
		},

		isShared : function(id) {
			if($('#f'+id).length > 0 && $('#f'+id).data('shared') == '1') {
				return true;
			}
			return false;
		},

        details : function(el) {
            var elem;
            if(elem = document.querySelector("#"+el)) {
				var title = elem.getAttribute("title").split("\n");
                Box.box_more = true;
                Box.reset();
                Box.Area = 1;
                Box.set("<div>\
                <p onclick=\"Box.right_click(event.clientX, event.clientY, '"+el+"')\"><i class='fa fa-chevron-left' aria-hidden='true'></i> &nbsp;&nbsp;<strong>"+txt.User.details+"</strong></p>\
                <hr><ul><li>"+txt.User.name+" : "+elem.getAttribute("data-title")+"</li>\
                <li>"+txt.User.path+" : "+elem.getAttribute("data-path")+"/</li>\
                <li>"+txt.User.type+" : "+txt.User.file+" <span class='ext_icon'></span></li>\
                <li>"+txt.User.size+" : "+title[0]+"</li>\
                <li>"+title[1]+"</li></ul></div>");

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
