<?php
	/* Recent view */
    use \library\MVC as l;
    $_t = new l\Template(self::$txt->Global->recents);

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
                <li>
                    <a href="Profile"><i class="fa fa-cog" aria-hidden="true"></i>&nbsp;
                    <?php echo self::$txt->UserMenu->settings; ?></a>
                </li>
                <li>
                    <span>
                        <input type="radio" id="display_list" name="display" checked>
                        <label for="display_list"><?php echo self::$txt->UserMenu->smallIcons; ?></label>
                    </span>
                    <span>
                        <input type="radio" id="display_mosaic" name="display">
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
            <img src="<?php echo MVC_ROOT; ?>/public/pictures/desktop/arrow.svg" class="arrow recents" />
        </section>
	</div>
<?php
    echo $_t->getFooter();
?>
