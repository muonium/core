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
	$_t->addJs("Interface/modules/Files");
	//$_t->addJs("Interface/modules/Folders");
	//$_t->addJs("Interface/modules/MessageBox");
	//$_t->addJs("Interface/modules/Selection");
	//$_t->addJs("Interface/modules/Toolbar");
	$_t->addJs("Interface/modules/Transfers");

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
    
    <div id="transfers" class="hide">
        <section id="top">
            <ul>
                <li><?php echo_h(self::$txt->Toolbar->transfers); ?></li>
                <li onclick="Transfers.minimize()"><i class="fa fa-window-minimize" aria-hidden="true"></i></li>
                <li onclick="Transfers.close()"><i class="fa fa-times" aria-hidden="true"></i></li>
            </ul>
        </section>

        <section id="toggle">
            <ul>
                <li class="selected" onclick="Transfers.showUp()"><?php echo_h(self::$txt->User->uploading); ?> <span class="transfers-up-circle">0</span></li>
                <li onclick="Transfers.showDl()"><?php echo_h(self::$txt->User->downloading); ?> <span class="transfers-dl-circle">0</span></li>
            </ul>
        </section>

        <section id="content">
            <div id="transfers_upload"><?php echo_h(self::$txt->User->nothing); ?></div>
            <div id="transfers_download"><?php echo_h(self::$txt->User->nothing); ?></div>
        </section>
    </div>
</body>
<script type="text/javascript">
    
	var f_dec = [];
    var i = 0;
    
    $(document).ready(function() {
		$('#dl').click(function() {
            new ShareDL();
		});
	});
    
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
                //Transferts.open()
                $('#transfers').fadeIn(400);
                
                //Transferts.showDL()
                $("#transfers #toggle ul > li:first-child").removeClass('selected');
                $("#transfers #toggle ul > li:last-child").addClass('selected');
                $("#transfers #content > #transfers_upload").hide();
                $("#transfers #content > #transfers_download").show();;
				//f_dec[i] = new Decryption("<?php //echo_h($infos['name']); ?>", <?php //echo_h($folderID); ?>, i);
				i++;
			}
		});
	});
</script>
<?php
    $_t->getFooter();
?>
