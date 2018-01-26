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
			var f = $('#'+id);
			if($(f).length) {
				if($(f).attr('data-title').length > 0 && $(f).attr('data-folder').length > 0) {
					var fname = $(f).attr('data-title');
					var ffolder = $(f).attr('data-folder');
					var ficon = ExtIcons.set(fname);

					$('.transfers_download').contents().filter(function() {
		    			return (this.nodeType == 3);
					}).remove();
					$('.transfers_download').append('<div id="div_download'+ i +'">'+
						'<i data-id="'+ i +'" class="fa fa-times-circle-o btn-abort" aria-hidden="true"></i>'+
						'<div>'+
							'<span class="fileinfo">'+ ficon + fname +'</span>'+
							'<span class="pct">0%</span>' +
							'<div class="progress_bar"><div class="used" style="width:0%"></div></div>'+
						'</div>'+
					'</div>');

					$('#div_download'+i+' .btn-abort').on('click', Files.abort);

					Transfers.open();
					Transfers.showDl();

					f_dec[i] = new Decryption(fname, ffolder, i);
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
					var f_salt = sjcl.codec.base64.toBits(chk[1]);
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
            var elem = $('#'+el);
            if($(elem).length) {
				var title = $(elem).attr('title').split("\n");
				var lastmod = title[1].split(': ');
				var shareLink = '<a class="mono blue" onclick="Selection.share(\''+el.substr(1)+'\')"><i class="fa fa-share" aria-hidden="true"></i> '+txt.RightClick.share+'</a>';
				var unshareLink = '<a class="mono blue" onclick="Selection.unshare(\''+el.substr(1)+'\')"><i class="fa fa-ban" aria-hidden="true"></i> '+txt.RightClick.unshare+'</a>';
                Box.box_more = true;
                Box.reset();
                Box.Area = 1;
                Box.set('<div class="details">\
                	<strong>'+txt.User.details+'</strong>\
                	<ul>\
						<li><span class="label">'+txt.User.name+':</span> '+$(elem).data("title")+'</li>\
                		<li><span class="label">'+txt.User.path+':</span> '+$(elem).data("path")+'/</li>\
                		<li><span class="label">'+txt.User.type+':</span> '+txt.User.file+'</li>\
                		<li><span class="label">'+txt.User.size+':</span> '+title[0]+'</li>\
                		<li><span class="label">'+lastmod[0]+'</span> '+lastmod[1]+'</li>\
					</ul>\
					<p><a class="mono blue" onclick="Selection.dl(\''+el+'\')"><i class="fa fa-download" aria-hidden="true"></i> '+txt.RightClick.dl+'</a></p>\
					<p>'+(Files.isShared(el.substr(1)) ? unshareLink : shareLink)+'</p>\
				</div>');
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
