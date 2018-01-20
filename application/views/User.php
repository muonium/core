<?php
    /* User view - main page (files management) */
    use \library\MVC as l;
    $_t = new l\Template(self::$txt->Global->user);

    $_t->addCss([
		/*'blue/blue',
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
	    'blue/tree',*/
	    'Interface/box',
	    'Interface/MessageBox',
	    'Interface/progress_bar',
		'2018/style',
		'2018/transfers',
		'2018/tree',
		'2018/selection'
	])->addJs([
		'Interface/modules/Arrows',
		'Interface/modules/Box',
		'Interface/modules/Decryption',
		'Interface/modules/Encryption',
		'Interface/modules/ExtIcons',
		'Interface/modules/Favorites',
		'Interface/modules/Files',
		'Interface/modules/Folders',
	    'Interface/modules/MessageBox',
		'Interface/modules/Move',
		'Interface/modules/Rm',
		'Interface/modules/Selection',
		'Interface/modules/Time',
	    'Interface/modules/Toolbar',
	    'Interface/modules/Transfers',
		'Interface/modules/Trash',
		'Interface/modules/Upload',
		'check',
		'src/crypto/sjcl',
		'Interface/idb.filesystem.min',
	    'Interface/Request',
		'Interface/interface'
	]);

	echo $_t->getHead();
	echo $_t->getHeader();
	echo $_t->getSidebar();
?>
    <div class="container-large">
		<span>
			<input type="radio" id="display_list" name="display">
			<label for="display_list"><?php echo self::$txt->UserMenu->smallIcons; ?></label>
		</span>
		<span>
			<input type="radio" id="display_mosaic" name="display" checked>
			<label for="display_mosaic"><?php echo self::$txt->UserMenu->largeIcons; ?></label>
		</span>

		<input type="checkbox" id="multisel" />
		<label for="multisel"><?php echo self::$txt->UserMenu->multiSelection; ?></label>

        <section id="desktop">
            <!-- Hidden upload form -->
            <form class="hidden">
                <input type="file" id="upFilesInput" name="files[]" multiple="multiple" class="hide" onchange="Upload.upFiles(this.files);" onclick="reset();" />
            </form>
            <!-- End -->

            <div id="returnArea"></div>
            <!-- mui contains all contents of interface : storage infos, link to parent folder, #tree (files and folders) ... -->
            <div id="mui">
                <?php echo self::$txt->Global->loading; ?>
            </div>
        </section>

        <section id="selection"></section>
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

    <div id="box" class="hide"></div>
    <div id="toolbar" class="hidden"></div> <!-- Box equivalent for mobile devices -->
	<a href="#" id="dl_decrypted"></a>
	<script>
		$(document).ready(UserLoader);
	</script>
<?php
    echo $_t->getFooter();
?>
