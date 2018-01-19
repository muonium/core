<?php
    /* User view - main page (files management) */
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
	    'Interface/box',
	    'Interface/MessageBox',
	    'Interface/progress_bar'
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
	    'object-watch',
		'src/crypto/sjcl',
		'Interface/idb.filesystem.min',
	    'Interface/Request',
		'Interface/interface'
	]);

	echo $_t->getHead();
	echo $_t->getHeader();
?>
    <!--<header>
        <div id="logo">
            <a href="https://muonium.io" target="_blank">
                <img src="public/pictures/logos/muonium_H_06.png" title="<?php echo self::$txt->Global->home; ?>" alt="<?php echo self::$txt->Global->home; ?>">
            </a>
        </div>
        <ul>
            <li onclick="Trash.switch()">
                <i class="fa fa-trash" aria-hidden="true"></i>&nbsp;
                <span id="button_trash"><?php echo self::$txt->User->trash_0; ?></span>
            </li>
            <li onclick="Upload.dialog()">
                <i class="fa fa-upload" aria-hidden="true"></i>&nbsp;
                <?php echo self::$txt->UserMenu->upload; ?>
            </li>
            <li onclick="showHelp()">?</li>
        </ul>
        <section id="language">
            <div>
                <?php $this->getLanguageSelector(); ?>
            </div>
        </section>
    </header>-->

    <div id="container">
        <section id="menu">
            <ul>
				<li><i class="fa fa-user" aria-hidden="true"></i>&nbsp;
					<?php echo_h($_SESSION['login']); ?>
				</li>
                <li>
                    <a href="Profile"><i class="fa fa-cog" aria-hidden="true"></i>&nbsp;
                    <?php echo self::$txt->UserMenu->settings; ?></a>
                </li>
				<li>
					<a href="Upgrade"><i class="fa fa-hdd-o" aria-hidden="true"></i>&nbsp;
					<?php echo self::$txt->UserMenu->moreStorage; ?></a>
				</li>
                <li>
                    <a href="Bug"><i class="fa fa-bug" aria-hidden="true"></i>&nbsp;
                    <?php echo self::$txt->Global->bug; ?></a>
                </li>
                <li>
                    <a onclick="Transfers.toggle()"><i class="fa fa-exchange" aria-hidden="true"></i>&nbsp;
                    <?php echo self::$txt->Toolbar->transfers; ?></a> <span class="transfers-circle">0</span>
                </li>
                <li id="display">
                    <span>
                        <input type="radio" id="display_list" name="display">
                        <label for="display_list"><?php echo self::$txt->UserMenu->smallIcons; ?></label>
                    </span>
                    <span>
                        <input type="radio" id="display_mosaic" name="display" checked>
                        <label for="display_mosaic"><?php echo self::$txt->UserMenu->largeIcons; ?></label>
                    </span>
                </li>
                <li id="multisel_desktop">
                    <input type="checkbox" id="multisel" />
                    <label for="multisel"><?php echo self::$txt->UserMenu->multiSelection; ?></label>
                </li>
                <!--<li>Stared</li>-->
                <!--<li>Shared</li>-->
            </ul>
            <div class="dragbar"></div>
        </section>

        <section id="desktop">
            <!-- Hidden upload form -->
            <form class="hide">
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
    <div id="toolbar"></div> <!-- Box equivalent for mobile devices -->
	<a href="#" id="dl_decrypted"></a>
<script>
	$(document).ready(function() {
		UserLoader();
	});
</script>
<?php
    echo $_t->getFooter();
?>
