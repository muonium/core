/* JS used on download page */

// require Decryption.js, Transfers.js

if(txt === undefined) {
	getJSON();
}
Decryption = Decryption();
Time = Time();
//Toolbar = Toolbar();
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
			$('#transfers').fadeIn(400);

			$("#transfers #toggle ul > li:first-child").removeClass('selected');
			$("#transfers #toggle ul > li:last-child").addClass('selected');
			$("#transfers #content > #transfers_upload").hide();
			$("#transfers #content > #transfers_download").show();
			f_dec[i] = new Decryption(fname, fid, i, uid, fek); // Call Decryption.js with 2 more parameters : uid and fek
			i++;
		}
	});

	$('body').on('keydown', function(event) {
		if(event.keyCode === 13) {
			$('#dl').click();
		}
	});
});
