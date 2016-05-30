<?php      
	/*
	* @name            : vSharing.php
	* @description     : Sharing view
	* @authors         : Romain Claveau <romain.claveau@protonmail.ch>, Dylan Clement <dylanclement7@protonmail.ch>
	*/

    $_t = new Template("Sharing");
    $_t->addCss("home_login");
    $_t->getHeader();
?>
        <header>
            <div id="logo"></div>
            <div id="user">
                <p><img src="<?php echo MVC_ROOT; ?>/public/pictures/header/bug.svg" /><br />Bug</p>
                <p><img src="<?php echo MVC_ROOT; ?>/public/pictures/header/help.svg" /><br />Aide</p>
                <p><img src="<?php echo MVC_ROOT; ?>/public/pictures/header/settings.svg" /><br />Options</p>
                <p><img src="<?php echo MVC_ROOT; ?>/public/pictures/header/user.svg" /><br />Profil</p>
            </div>
        </header>
        
        <section id="toolbar">
            <div onclick="QuantaCloud.clickEvent(this,'DefaultAction','Recent')" id="toolbar_button_recents">
                <img src="<?php echo MVC_ROOT; ?>/public/pictures/toolbar/recent.svg" /><br />Récents
            </div>
            <div onclick="QuantaCloud.clickEvent(this,'DefaultAction','Favoris')" id="toolbar_button_favorite">
                <img src="<?php echo MVC_ROOT; ?>/public/pictures/toolbar/favorite.svg" /><br />Favoris
            </div>
            <div onclick="QuantaCloud.clickEvent(this,'DefaultAction','Accueil')" id="toolbar_button_general">
                <img src="<?php echo MVC_ROOT; ?>/public/pictures/toolbar/folder.svg" /><br />Général
            </div>
            <div class="selected" onclick="QuantaCloud.clickEvent(this,'DefaultAction','Partage')" id="toolbar_button_share">
                <img src="<?php echo MVC_ROOT; ?>/public/pictures/toolbar/share.svg" /><br />Partagés
            </div>
            <div onclick="QuantaCloud.clickEvent(this,'DefaultAction','Transfert')" id="toolbar_button_transfers">
                <img src="<?php echo MVC_ROOT; ?>/public/pictures/toolbar/transfer.svg" /><br />Transferts
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
<?php
    $t->getFooter();
?>
        