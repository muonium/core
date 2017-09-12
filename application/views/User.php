<?php
    /*
	* @name            : User.php
	* @description     : User view (files management)
	* @authors         : Romain Claveau <romain.claveau@protonmail.ch>, Dylan Clement <dylanclement7@protonmail.ch>
	*/
    use \library\MVC as l;
    $_t = new l\Template($this->txt->Global->user);
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
	$_t->addJs("Interface/modules/Arrows");
	$_t->addJs("Interface/modules/Box");
	$_t->addJs("Interface/modules/Decryption");
	$_t->addJs("Interface/modules/Encryption");
	$_t->addJs("Interface/modules/ExtIcons");
	$_t->addJs("Interface/modules/Favorites");
	$_t->addJs("Interface/modules/Files");
	$_t->addJs("Interface/modules/Folders");
    $_t->addJs("Interface/modules/MessageBox");
	$_t->addJs("Interface/modules/Move");
	$_t->addJs("Interface/modules/Rm");
	$_t->addJs("Interface/modules/Selection");
	$_t->addJs("Interface/modules/Time");
    $_t->addJs("Interface/modules/Toolbar");
    $_t->addJs("Interface/modules/Transfers");
	$_t->addJs("Interface/modules/Trash");
	$_t->addJs("Interface/modules/Upload");

	$_t->addJs("check");
    $_t->addJs("object-watch");
	$_t->addJs("src/crypto/sjcl");
	$_t->addJs("Interface/idb.filesystem.min");
    $_t->addJs("Interface/Request");
	$_t->addJs("Interface/interface");
    $_t->getHeader();
?>
<body onload="UserLoader()">
    <header>
        <div id="logo">
            <a href="https://muonium.io" target="_blank">
                <img src="public/pictures/logos/muonium_H_06.png" title="<?php echo $this->txt->Global->home; ?>" alt="<?php echo $this->txt->Global->home; ?>">
            </a>
        </div>
        <ul>
            <li onclick="Trash.switch()">
                <i class="fa fa-trash" aria-hidden="true"></i>&nbsp;
                <span id="button_trash"><?php echo_h($this->txt->User->trash_0); ?></span>
            </li>
            <li onclick="Upload.dialog()">
                <i class="fa fa-upload" aria-hidden="true"></i>&nbsp;
                <?php echo_h($this->txt->UserMenu->upload); ?>
            </li>
            <li>
                <a href="Logout"><i class="fa fa-sign-out" aria-hidden="true"></i>&nbsp;
                <?php echo_h($this->txt->UserMenu->logout); ?></a>
            </li>
            <li onclick="showHelp()">?</li>
        </ul>
        <section id="language">
            <div>
                <?php $this->getLanguageSelector(); ?>
            </div>
        </section>
    </header>

    <div id="container">
        <section id="menu">
            <ul>
				<li><i class="fa fa-user" aria-hidden="true"></i>&nbsp;
					<?php echo_h($_SESSION['login']); ?>
				</li>
                <li>
                    <a href="Profile"><i class="fa fa-cog" aria-hidden="true"></i>&nbsp;
                    <?php echo_h($this->txt->UserMenu->settings); ?></a>
                </li>
				<li>
					<a href="Upgrade"><i class="fa fa-hdd-o" aria-hidden="true"></i>&nbsp;
					<?php echo_h($this->txt->UserMenu->moreStorage); ?></a>
				</li>
                <li>
                    <a href="Bug"><i class="fa fa-bug" aria-hidden="true"></i>&nbsp;
                    <?php echo_h($this->txt->Global->bug); ?></a>
                </li>
                <li>
                    <a onclick="Transfers.toggle()"><i class="fa fa-exchange" aria-hidden="true"></i>&nbsp;
                    <?php echo_h($this->txt->Toolbar->transfers); ?></a> <span class="transfers-circle">0</span>
                </li>
                <li id="display">
                    <span>
                        <input type="radio" id="display_list" name="display" checked>
                        <label for="display_list"><?php echo_h($this->txt->UserMenu->smallIcons); ?></label>
                    </span>
                    <span>
                        <input type="radio" id="display_mosaic" name="display">
                        <label for="display_mosaic"><?php echo_h($this->txt->UserMenu->largeIcons); ?></label>
                    </span>
                </li>
                <li id="multisel_desktop">
                    <input type="checkbox" id="multisel" />
                    <label for="multisel"><?php echo_h($this->txt->UserMenu->multiSelection); ?></label>
                </li>
                <!--<li>Stared</li>-->
                <!--<li>Shared</li>-->
            </ul>
            <div class="dragbar"></div>
        </section>

        <section id="desktop">
            <!-- Hidden upload form -->
            <form style="display:none">
                <input type="file" id="upFilesInput" name="files[]" multiple="multiple" style="display:none" onchange="Upload.upFiles(this.files);" onclick="reset();" />
            </form>
            <!-- End -->

            <div id="returnArea"></div>
            <!-- mui contains all contents of interface : storage infos, link to parent folder, #tree (files and folders) ... -->
            <div id="mui">
                <?php echo_h($this->txt->Global->loading); ?>
            </div>
        </section>

        <section id="selection"></section>
    </div>

    <div id="transfers" style="display:none">
        <section id="top">
            <ul>
                <li><?php echo_h($this->txt->Toolbar->transfers); ?></li>
                <li onclick="Transfers.minimize()"><i class="fa fa-window-minimize" aria-hidden="true"></i></li>
                <li onclick="Transfers.close()"><i class="fa fa-times" aria-hidden="true"></i></li>
            </ul>
        </section>

        <section id="toggle">
            <ul>
                <li class="selected" onclick="Transfers.showUp()"><?php echo_h($this->txt->User->uploading); ?> <span class="transfers-up-circle">0</span></li>
                <li onclick="Transfers.showDl()"><?php echo_h($this->txt->User->downloading); ?> <span class="transfers-dl-circle">0</span></li>
            </ul>
        </section>

        <section id="content">
            <div id="transfers_upload"><?php echo_h($this->txt->User->nothing); ?></div>
            <div id="transfers_download"><?php echo_h($this->txt->User->nothing); ?></div>
        </section>
    </div>

    <div id="box" style="display:none"></div>
    <div id="toolbar"></div> <!-- Box equivalent for mobile devices -->
	<a href="#" id="dl_decrypted"></a>
</body>
<?php
    $_t->getFooter();
?>
