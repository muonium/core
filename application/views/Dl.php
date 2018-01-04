<?php
	/* Download a shared file */
	use \library\MVC as l;
	$_t = new l\Template(self::$txt->Global->user);

	$_t->addCss([
		'blue/blue',
		'blue/container',
		'blue/dragbar',
		'blue/header',
		'blue/inputs',
		'blue/menu',
		'blue/section-desktop',
		'blue/section-large-content',
		'blue/selection',
		'blue/toolbar',
		'blue/transfers',
		'blue/tree',
		'Interface/progress_bar'
	])->addJs([
		'Interface/modules/Decryption',
		'Interface/modules/Files',
		'Interface/modules/Time',
		'Interface/modules/Transfers',
		'object-watch',
		'src/crypto/sjcl',
		'Interface/idb.filesystem.min',
		'Dl'
	]);

	echo $_t->getHead();
	echo $_t->getHeader();
?>
    <div id="container">
        <section id="large-content">
            <h1><?php echo_h($infos['name']); ?></h1>
			<p><?php echo_h(str_replace('[login]', $infos['login'], self::$txt->User->uploadedBy)); echo ' '.date('Y-m-d G:i', $infos['last_modification']); ?></p>
			<p><?php echo self::$txt->User->size; echo '&nbsp;: '.$filesize; ?></p>

			<p><?php echo self::$txt->Register->password; ?>&nbsp;: <input type="password" id="password"></p>
			<p>
				<button id="dl"
					data-dk="<?php echo $infos['dk']; ?>"
					data-fname="<?php echo_h($infos['name']); ?>"
					data-fid="<?php echo $infos['folder_id']; ?>"
					data-uid="<?php echo $infos['id_owner']; ?>"
				>
				<?php echo self::$txt->RightClick->dl; ?>
				</button>
			</p>

			<p id="msg"></p>
        </section>
    </div>

    <div id="transfers" class="hide">
        <section id="top">
            <ul>
                <li><?php echo self::$txt->Toolbar->transfers; ?></li>
                <li onclick="Transfers.minimize()"><i class="fa fa-window-minimize" aria-hidden="true"></i></li>
                <li onclick="Transfers.close()"><i class="fa fa-times" aria-hidden="true"></i></li>
            </ul>
        </section>

        <section id="toggle">
            <ul>
                <li class="selected" onclick="Transfers.showUp()"><?php echo self::$txt->User->uploading; ?> <span class="transfers-up-circle">0</span></li>
                <li onclick="Transfers.showDl()"><?php echo self::$txt->User->downloading; ?> <span class="transfers-dl-circle">0</span></li>
            </ul>
        </section>

        <section id="content">
            <div id="transfers_upload"><?php echo self::$txt->User->nothing; ?></div>
            <div id="transfers_download"><?php echo self::$txt->User->nothing; ?></div>
        </section>
    </div>
	<a href="#" id="dl_decrypted"></a>
<?php
    echo $_t->getFooter();
?>
