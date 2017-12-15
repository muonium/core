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
	$_t->addCss("Interface/progress_bar");

	// JS Modules
	$_t->addJs("Interface/modules/Decryption");
	$_t->addJs("Interface/modules/Files");
	//$_t->addJs("Interface/modules/Folders");
	//$_t->addJs("Interface/modules/Selection");
	//$_t->addJs("Interface/modules/Toolbar");
	$_t->addJs("Interface/modules/Time");
	$_t->addJs("Interface/modules/Transfers");

	//$_t->addJs("check");
	$_t->addJs("object-watch");
	$_t->addJs("src/crypto/sjcl");
	$_t->addJs("Interface/idb.filesystem.min");
	$_t->addJs("Dl");
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
			<p><?php echo_h(str_replace('[login]', $infos['login'], self::$txt->User->uploadedBy)); echo ' '.date('Y-m-d G:i', $infos['last_modification']); ?></p>
			<p><?php echo_h(self::$txt->User->size); echo '&nbsp;: '.$filesize; ?></p>

			<p><?php echo_h(self::$txt->Register->password); ?>&nbsp;: <input type="text" id="password"></p>
			<p>
				<button id="dl"
					data-dk="<?php echo $infos['dk']; ?>"
					data-fname="<?php echo_h($infos['name']); ?>"
					data-fid="<?php echo $infos['folder_id']; ?>"
					data-uid="<?php echo $infos['id_owner']; ?>"
				>
				<?php echo_h(self::$txt->RightClick->dl); ?>
				</button>
			</p>

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
<?php
    $_t->getFooter();
?>
