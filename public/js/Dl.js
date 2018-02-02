/* JS used on download page */

// require Decryption.js, Transfers.js

if(txt === undefined) {
	getJSON();
}
Decryption = Decryption();
ExtIcons = ExtIcons();
Files = Files();
Time = Time();
Transfers = Transfers();

var f_dec = [];
var i = 0;

$(document).ready(function() {
	$('#password').focus();

	$('#dl').click(function() {
		var fname = $(this).data('fname'),
			fid = $(this).data('fid'),
			uid = $(this).data('uid');
		var err = false;
		var fek;
		var mdp = $('#password').val();
		var packet = $(this).data('dk');
		var c = packet.split(':');
		var enc_fek = sjcl.codec.base64.toBits(c[0]);
		var salt = sjcl.codec.base64.toBits(c[1]);
		var aDATA= sjcl.codec.base64.toBits(c[2]);
		var iv = sjcl.codec.base64.toBits(c[3]);

		var dk = sjcl.misc.pbkdf2(mdp, salt, 7000, 256);
		var enc = new sjcl.cipher.aes(dk);
		try {
			fek = sjcl.mode.gcm.decrypt(enc, enc_fek, iv, aDATA, 128);
		} catch(e) {
			err = true;
			$('#msg').html(txt.User.badPass);
		}

		if(!err) {
			$('#msg').html('');

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

			f_dec[i] = new Decryption(fname, fid, i, uid, fek); // Call Decryption.js with 2 more parameters : uid and fek
			i++;
		}
	});

	$('body').on('keydown', function(event) {
		if(event.keyCode === 13) {
			$('#dl').click();
			event.preventDefault();
		}
	});
});
