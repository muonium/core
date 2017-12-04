<?php
	use \library\MVC as l;
	$_t = new l\Template(self::$txt->Global->user);
	$_t->addCss("blue/blue");
	$_t->addCss("blue/container");
	$_t->addCss("blue/dragbar");
	$_t->addCss("blue/header");
	$_t->addCss("blue/inputs");
	$_t->addCss("blue/menu");
	$_t->addCss("blue/section-desktop");
	$_t->addCss("blue/section-large-content");
	$_t->addCss("blue/selection");
	$_t->addCss("blue/toolbar");
	$_t->addCss("blue/transfers");
	$_t->addCss("blue/tree");
	$_t->addCss("Interface/box");
	$_t->addCss("Interface/MessageBox");
	$_t->addCss("Interface/progress_bar");

	// JS Modules
	//$_t->addJs("Interface/modules/Box");
	$_t->addJs("Interface/modules/Decryption");
	$_t->addJs("Interface/modules/Encryption");
	//$_t->addJs("Interface/modules/Files");
	//$_t->addJs("Interface/modules/Folders");
	//$_t->addJs("Interface/modules/MessageBox");
	//$_t->addJs("Interface/modules/Selection");
	//$_t->addJs("Interface/modules/Toolbar");
	//$_t->addJs("Interface/modules/Transfers");

	//$_t->addJs("check");
	//$_t->addJs("object-watch");
	$_t->addJs("src/crypto/sjcl");
	$_t->addJs("Interface/idb.filesystem.min");
	//$_t->addJs("Interface/Request");
	//$_t->addJs("Interface/interface");
	$_t->getHeader();
?>
<body class="grey">
    <header>
        <div id="logo">
            <a href="https://muonium.io" target="_blank">
                <img src="public/pictures/logos/muonium_H_06.png">
            </a>
        </div>
        <ul>
            <li><a href="User"><?php echo self::$txt->Global->back; ?></a></li>
        </ul>
        <section id="language">
            <div>
                <?php $this->getLanguageSelector(); ?>
            </div>
        </section>
    </header>

    <div id="container">
        <section id="large-content">
            <h1><?php echo_h($infos['name']); ?></h1>

			<p>Uploaded by <?php echo_h($infos['login']); echo ' on '.date('Y-m-d G:i', $infos['last_modification']); ?></p>
			<p>Size : <?php echo $filesize; ?></p>

			<p>Password : <input type="text" id="password"></p>
			<p><button id="dl">DL</button></p>

			<p id="msg"></p>
        </section>
    </div>
</body>
<script type="text/javascript">
	$(document).ready(function() {
		$('#dl').click(function() {
			var err = false;
			var mdp = $('#password').val();
			var packet = '<?php echo $infos['dk']; ?>';
			var c = packet.split(':');
		 	var enc_fek = sjcl.codec.base64.toBits(c[0]);
		 	var salt = sjcl.codec.base64.toBits(c[1]);
		 	var aDATA= sjcl.codec.base64.toBits(c[2]);
		 	var iv = sjcl.codec.base64.toBits(c[3]);

			//on recalcule la clé dérivée`dk` à partir du mdp `mdp`
		 	var dk = sjcl.misc.pbkdf2(mdp, salt, 7000, 256);
			var enc = new sjcl.cipher.aes(dk);
			try {
		 		var fek = sjcl.mode.gcm.decrypt(enc, enc_fek, iv, aDATA, 128);
			} catch(e) {
				err = true;
				$('#msg').html('Bad password');
			}

			if(!err) {
				$('#msg').html('Password ok');
			}
		});
	});
</script>
<?php
    $_t->getFooter();
?>
