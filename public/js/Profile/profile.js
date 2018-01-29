/* profile.js */

window.onload = function() {
    // Get txt from user's language json (language.js)
    getJSON();
	var theme = 'light';
	if(getCookie('theme') === 'dark') {
		theme = 'dark';
	}
    $('#'+theme).prop('checked', true);
};

var changeLogin = function(e) {
	e.preventDefault();
    var login = $('#new_login').val();
    var returnArea = $('#changeLoginReturn');
    $(returnArea).html('');

	$.post('Profile/ChangeLogin', {login: encodeURIComponent(login)}, function(data) {
		if(data.substr(0, 3) === 'ok@') {
			data = data.split('@')[1];
			$('#username').html(htmlEntities(login));
		}
		$(returnArea).html(data);
	});
};

var changeMail = function(e) {
	e.preventDefault();
    var mail = $('#new_mail').val();
    var returnArea = $('#changeMailReturn');
	$(returnArea).html('');

    $.post('Profile/ChangeMail', {mail: encodeURIComponent(mail)}, function(data) {
		if(data.substr(0, 3) === 'ok@') {
			data = data.split('@')[1];
			$('#email').html(htmlEntities(mail));
		}
		$(returnArea).html(data);
	});
};

var changePassword = function(e) {
	e.preventDefault();
    var returnArea = $('#changePasswordReturn');
    var old_pwd = $('#old_pwd').val();
    var new_pwd = $('#new_pwd').val();
    var pwd_confirm = $('#pwd_confirm').val();
	$(returnArea).html('');

    if(new_pwd.length < 6 || pwd_confirm !== new_pwd) {
        $(returnArea).html(txt.Register.form);
    } else {
		// Use decodeURIComponent with mui_hash in jQuery because it already encodes it.
		$.post('Profile/ChangePassword', {
			old_pwd: decodeURIComponent(mui_hash(old_pwd)),
			new_pwd: decodeURIComponent(mui_hash(new_pwd)),
			pwd_confirm: decodeURIComponent(mui_hash(pwd_confirm))
		}, function(data) {
			$(returnArea).html(data);
		});
    }
};

var changeCek = function(e) {
	e.preventDefault();
    var returnArea = $('#changePassPhraseReturn');
    var old_pp = $('#old_pp').val();
    var new_pp = $('#new_pp').val();
    var pp_confirm = $('#pp_confirm').val();
	var current_pp = sessionStorage.getItem('kek');
	$(returnArea).html('');

	var cek = sessionStorage.getItem('cek'); ///we get the CEK from sessionStorage
	if(cek == null || current_pp == null) {
		window.location.href = ROOT+"Logout";
	}

	if(old_pp != current_pp) {
		$(returnArea).html(txt.Profile.badOldPassphrase);
	} else if(new_pp.length < 6) {
		$(returnArea).html(txt.Register.form);
	} else {
		//crypto parameters, don't touch
		var aDATA = sjcl.random.randomWords(4); //authentication data - 128 bits
		var initVector = sjcl.random.randomWords(4); //initialization vector - 128 bits
		var salt = sjcl.random.randomWords(4); //salt - 128 bits

		//we encrypt the CEK under the new passphrase (alias "KEK" -Key Encryption Key)
		var encryptedCek = sjcl.encrypt(new_pp, cek, {mode:'gcm', iter:7000, iv:initVector, ks:256, adata:aDATA, ts:128, salt:salt});
			encryptedCek = base64.encode(encryptedCek); //we b64encode it to store it in the DB

		$.post('Profile/ChangeCek', {cek: encryptedCek}, function(data) {
			if(data.substr(0, 3) === 'ok@') {
				sessionStorage.setItem("kek", new_pp);
				data = data.split('@')[1];
			}
			$(returnArea).html(data);
		});
	}
};

var switchTheme = function() {
    var theme = $('input[name="theme"]:checked').attr('id') === 'dark' ? 'dark' : 'light';
	$('#theme-css').attr('href', function(i,v) {
		if(theme === 'dark') {
			v = v.replace('/light.css', '/dark.css');
		} else {
			v = v.replace('/dark.css', '/light.css');
		}
		return v;
	});
    setCookie('theme', theme, 365);
};

var changeAuth = function() {
    var returnArea = $('#changeAuthReturn');
    var doubleAuth = $('#doubleAuth').prop('checked');

    $.post('Profile/changeAuth', {doubleAuth: doubleAuth}, function(data) {
		$(returnArea).html(data);
	});
};

var deleteUser = function() {
    var returnArea = $('#deleteUserReturn');
    $.post('Profile/DeleteUser', {deleteUser: 'ok'}, function(data) {
		if(data.substr(0, 3) === 'ok@') {
			window.location.href=ROOT+"Logout";
		} else {
			$(returnArea).html(data);
		}
	});
};

var ConfirmDelete = function(e) {
	e.preventDefault();
	if(confirm(txt.Profile.accountDeletionConfirm)) {
		deleteUser();
	}
};
