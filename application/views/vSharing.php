<?php      
	/*
	* @name            : vSharing.php
	* @description     : Sharing view
	* @authors         : Romain Claveau <romain.claveau@protonmail.ch>, Dylan Clement <dylanclement7@protonmail.ch>
	*/

    $_t = new Template($this->txt->Global->sharing);
    $_t->addCss("home_global");
    $_t->getHeader();
?>
<body>
        <header>
            <div id="logo"></div>
            <div id="user">
                <p><img src="<?php echo MVC_ROOT; ?>/public/pictures/header/bug.svg" /><br /><?php echo $this->txt->UserMenu->bug; ?></p>
                <p><img src="<?php echo MVC_ROOT; ?>/public/pictures/header/help.svg" /><br /><?php echo $this->txt->UserMenu->help; ?></p>
                <p><img src="<?php echo MVC_ROOT; ?>/public/pictures/header/settings.svg" /><br /><?php echo $this->txt->UserMenu->settings; ?></p>
                <p><img src="<?php echo MVC_ROOT; ?>/public/pictures/header/user.svg" /><br /><?php echo $this->txt->UserMenu->profile; ?></p>
            </div>
    </header>
    
        <section id="language">
            <div>
                <?php $this->getLanguageSelector(); ?>
            </div>
        </section>
        
        <section id="toolbar">
            <div onclick="QuantaCloud.clickEvent(this,'DefaultAction','Recent')" id="toolbar_button_recents">
                <img src="<?php echo MVC_ROOT; ?>/public/pictures/toolbar/recent.svg" /><br /><?php echo $this->txt->Toolbar->recents; ?>
            </div>
            <div class="selected" onclick="QuantaCloud.clickEvent(this,'DefaultAction','Favorites')" id="toolbar_button_favorite">
                <img src="<?php echo MVC_ROOT; ?>/public/pictures/toolbar/favorite.svg" /><br /><?php echo $this->txt->Toolbar->favorites; ?>
            </div>
            <div onclick="QuantaCloud.clickEvent(this,'DefaultAction','Home')" id="toolbar_button_general">
                <img src="<?php echo MVC_ROOT; ?>/public/pictures/toolbar/folder.svg" /><br /><?php echo $this->txt->Toolbar->general; ?>
            </div>
            <div onclick="QuantaCloud.clickEvent(this,'DefaultAction','Sharing')" id="toolbar_button_share">
                <img src="<?php echo MVC_ROOT; ?>/public/pictures/toolbar/share.svg" /><br /><?php echo $this->txt->Toolbar->shared; ?>
            </div>
            <div onclick="QuantaCloud.clickEvent(this,'DefaultAction','Transfer')" id="toolbar_button_transfers">
                <img src="<?php echo MVC_ROOT; ?>/public/pictures/toolbar/transfer.svg" /><br /><?php echo $this->txt->Toolbar->transfers; ?>
            </div>
        </section>
        
        <section id="desktop">
            <img src="<?php echo MVC_ROOT; ?>/public/pictures/desktop/arrow.svg" class="arrow shares" />
            
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
    $t->getFooter();
?>
        