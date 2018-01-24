<?php
	/* Download a shared file */
	use \library\MVC as l;
	$_t = new l\Template(self::$txt->Global->user);

	$_t->addCss([
		'2018/style'
	])->addJs([
		'Interface/modules/ExtIcons',
		'Interface/modules/Decryption',
		'Interface/modules/Files',
		'Interface/modules/Time',
		'Interface/modules/Transfers',
		'src/crypto/sjcl',
		'Interface/idb.filesystem.min',
		'Dl'
	]);

	echo $_t->getHead();
	echo $_t->getHeader();
?>
    <div class="container-large">
		<?php if(!isset($_SESSION['id'])) { ?>
			<div class="info">
				<?php echo self::$txt->Register->donthaveaccount; ?>
				<a href="<?php echo MVC_ROOT; ?>/Register"><?php echo self::$txt->Register->create; ?></a>
			</div>
		<?php } ?>

		<div class="center mtop mono">
	        <h1 class="dl-filename"><?php echo_h($infos['name']); ?></h1>

			<p class="dl-info">
				<?php
					echo_h(str_replace('[login]', $infos['login'], self::$txt->User->uploadedBy));
					echo ' '.date(self::$txt->Dates->date.' '.self::$txt->Dates->time, $infos['last_modification']);
				?>
			</p>
			<p class="dl-info"><?php echo self::$txt->User->size; echo ': '.$filesize; ?></p>

			<p class="input-small">
				<input type="password" id="password" placeholder="<?php echo self::$txt->Register->password; ?>">
				<label class="fa fa-lock" for="password"></label>
			</p>
			<p>
				<button id="dl" class="btn mtop"
					data-dk="<?php echo $infos['dk']; ?>"
					data-fname="<?php echo_h($infos['name']); ?>"
					data-fid="<?php echo $infos['folder_id']; ?>"
					data-uid="<?php echo $infos['id_owner']; ?>"
				>
				<?php echo self::$txt->RightClick->dl; ?>
				</button>
			</p>

			<p id="msg"></p>
		</div>
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
