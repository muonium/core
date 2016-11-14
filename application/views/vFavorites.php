<?php
	/*
	* @name            : vFavorites.php
	* @description     : Favorites view
	* @authors         : Romain Claveau <romain.claveau@protonmail.ch>, Quentin THOMAS <q.thomas54@protonmail.com>, Dylan Clement <dylanclement7@protonmail.ch>
	*/
    use \library\MVC as l;
    $_t = new l\Template($this->txt->Global->favorites);
	$_t->addCss("home_global");
    $_t->addCss("Interface/new_design");
    $_t->addCss("Interface/box");
    $_t->addJs("Interface/Request");
	$_t->addJs("Interface/interface");
    $_t->getHeader();
?>
<body>
        <header>
            <div id="logo"></div>
            <div id="user">
                <p><img src="<?php echo MVC_ROOT; ?>/public/pictures/header/bug.svg" /><br /><?php echo_h($this->txt->UserMenu->bug); ?></p>
                <p><img src="<?php echo MVC_ROOT; ?>/public/pictures/header/help.svg" /><br /><?php echo_h($this->txt->UserMenu->help); ?></p>
                <p><img src="<?php echo MVC_ROOT; ?>/public/pictures/header/settings.svg" /><br /><?php echo_h($this->txt->UserMenu->settings); ?></p>
                <p><img src="<?php echo MVC_ROOT; ?>/public/pictures/header/user.svg" /><br /><?php echo_h($this->txt->UserMenu->profile); ?></p>
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
            <div class="selected" onclick="Request.load('Favorites', 'DefaultAction')" id="toolbar_button_favorite">
                <img src="<?php echo MVC_ROOT; ?>/public/pictures/toolbar/favorite.svg" /><br /><?php echo_h($this->txt->Toolbar->favorites); ?>
            </div>
            <div onclick="Request.load('User', 'DefaultAction')" id="toolbar_button_general">
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
			<div id="mui">
				<?php echo $favorites; ?>
            </div>

            <img src="<?php echo MVC_ROOT; ?>/public/pictures/desktop/arrow.svg" class="arrow favorites" />

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
                    </div>
                </div>
                <div id="rightPanel">
                    <table>
                    </table>
                </div>
            </div>
        </section>
</body>
<?php
    $_t->getFooter();
?>
