<?php
    /*
	* @name            : vUser.php
	* @description     : User view (files management)
	* @authors         : Romain Claveau <romain.claveau@protonmail.ch>, Dylan Clement <dylanclement7@protonmail.ch>
	*/
    use \library\MVC as l;
    $_t = new l\Template($this->txt->Global->profile);
    $_t->addCss("home_global");
    $_t->addCss("Interface/new_design");
    $_t->addCss("Interface/box");
    $_t->addJs("Interface/Request");
    $_t->addJs("Interface/interface");
    $_t->getHeader();
?>
<body onload="UserLoader()">
        <header>
            <div id="logo"></div>
            <div id="user">
                <p><img src="<?php echo MVC_ROOT; ?>/public/pictures/header/bug.svg" /><br /><?php echo_h($this->txt->UserMenu->bug); ?></p>
                <p><img src="<?php echo MVC_ROOT; ?>/public/pictures/header/help.svg" /><br /><?php echo_h($this->txt->UserMenu->help); ?></p>
                <p><img src="<?php echo MVC_ROOT; ?>/public/pictures/header/settings.svg" /><br /><?php echo_h($this->txt->UserMenu->settings); ?></p>
                <p><a href="<?php echo MVC_ROOT; ?>/Profile"><img src="<?php echo MVC_ROOT; ?>/public/pictures/header/user.svg" /><br /><?php echo_h($this->txt->UserMenu->profile); ?></a></p>
                <p><a href="<?php echo MVC_ROOT; ?>/Logout"><img src="<?php echo MVC_ROOT; ?>/public/pictures/header/user.svg" /><br /><?php echo_h($this->txt->UserMenu->logout); ?></a></p>
            </div>
        </header>

        <section id="language">
            <div>
                <?php $this->getLanguageSelector(); ?>
            </div>
        </section>

        <section id="toolbar">
            <div onclick="Request.load('Recent', 'DefaultAction')" id="toolbar_button_recents">
                <img src="<?php echo MVC_ROOT; ?>/public/pictures/toolbar/recent.svg" /><br /><?php echo_h($this->txt->Toolbar->recents); ?>
            </div>
            <div onclick="Request.load('Favorites', 'DefaultAction')" id="toolbar_button_favorite">
                <img src="<?php echo MVC_ROOT; ?>/public/pictures/toolbar/favorite.svg" /><br /><?php echo_h($this->txt->Toolbar->favorites); ?>
            </div>
            <div class="selected" onclick="Request.load('User', 'DefaultAction')" id="toolbar_button_general">
                <img src="<?php echo MVC_ROOT; ?>/public/pictures/toolbar/folder.svg" /><br /><?php echo_h($this->txt->Toolbar->general); ?>
            </div>
            <div onclick="Request.load('Sharing', 'DefaultAction')" id="toolbar_button_share">
                <img src="<?php echo MVC_ROOT; ?>/public/pictures/toolbar/share.svg" /><br /><?php echo_h($this->txt->Toolbar->shared); ?>
            </div>
            <div onclick="Request.load('Transfer', 'DefaultAction')" id="toolbar_button_transfers">
                <img src="<?php echo MVC_ROOT; ?>/public/pictures/toolbar/transfer.svg" /><br /><?php echo_h($this->txt->Toolbar->transfers); ?>
            </div>
        </section>

        <section id="desktop">
            <button id="button_trash" onclick="Trash.switch()"><?php echo_h($this->txt->User->trash_0); ?></button>
            <!-- Hidden upload form -->
            <form style="display:none">
                <input type="file" id="upFilesInput" name="files[]" multiple="multiple" style="display:none" onchange="Upload.upFiles(this.files);" onclick="reset();" />
            </form>
            <!-- End -->

            <div id="returnArea"></div>
            <!-- progress contains progress status of uploaded files -->
            <div id="progress"></div>
            <!-- mui contains all contents of interface : storage infos, link to parent folder, #tree (files and folders) ... -->
            <div id="mui">
                <?php echo_h($this->txt->Global->loading); ?>
            </div>
            <img src="<?php echo MVC_ROOT; ?>/public/pictures/desktop/arrow.svg" class="arrow general" />

            <div id="desktop_general" class="content">
                <div id="nav">
                    <span class="content">
                        <span class="dir">Home</span>
                        <span class="separator">&gt;</span>
                    </span>
                </div>
                <div id="leftPanel">
                    <div id="listTypes">
                        <p><img src="<?php echo MVC_ROOT; ?>/public/pictures/desktop/list/list.svg" /></p>
                        <p><img src="<?php echo MVC_ROOT; ?>/public/pictures/desktop/list/grid.svg" /></p>
                        <p><img src="<?php echo MVC_ROOT; ?>/public/pictures/desktop/list/atomic.svg" /></p>
                    </div>
                    <div id="actions">
                        <div class="action"><p><img src="<?php echo MVC_ROOT; ?>/public/pictures/desktop/actions/create_file.svg" /></p></div>
                        <div class="action"><p><img src="<?php echo MVC_ROOT; ?>/public/pictures/desktop/actions/create_folder.svg" /></p></div>
                        <div class="action"><p><img src="<?php echo MVC_ROOT; ?>/public/pictures/desktop/actions/upload.svg" /></p></div>
                    </div>
                </div>
                <div id="rightPanel">
                </div>

            </div>
        </section>
    <div id="box" style="display:none"></div>
</body>
<?php
    $_t->getFooter();
?>
