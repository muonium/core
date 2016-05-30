<?php    
	/*
	* @nom             : Desktop.php
	* @description     : Interface de gestion des fichiers et dossiers
	* @authors         : Romain Claveau <romain.claveau@protonmail.ch>, ...
	*/
?>
        <header>
            <div id="logo"></div>
            <div id="user">
                <p><img src="./public/pictures/header/bug.svg" /><br />Bug</p>
                <p><img src="./public/pictures/header/help.svg" /><br />Aide</p>
                <p><img src="./public/pictures/header/settings.svg" /><br />Options</p>
                <p><img src="./public/pictures/header/user.svg" /><br />Profil</p>
            </div>
        </header>
        
        <section id="toolbar">
            <div onclick="QuantaCloud.clickEvent(this,'DefaultAction','Recent')" id="toolbar_button_recents"><img src="./public/pictures/toolbar/recent.svg" /><br />Récents</div>
            <div onclick="QuantaCloud.clickEvent(this,'DefaultAction','Favoris')" id="toolbar_button_favorite"> <img src="./public/pictures/toolbar/favorite.svg" /><br />Favoris</div>
            <div onclick="QuantaCloud.clickEvent(this,'DefaultAction','Accueil')" id="toolbar_button_general"><img src="./public/pictures/toolbar/folder.svg" /><br />Général</div>
            <div onclick="QuantaCloud.clickEvent(this,'DefaultAction','Partage')" id="toolbar_button_share"><img src="./public/pictures/toolbar/share.svg" /><br />Partagés</div>
            <div class="selected" onclick="QuantaCloud.clickEvent(this,'DefaultAction','Transfert')" id="toolbar_button_transfers"><img src="./public/pictures/toolbar/transfer.svg" /><br />Transferts</div>
        </section>
        
        <section id="desktop">
            <img src="./public/pictures/desktop/arrow.svg" class="arrow transfers" />
        
            
            <div id="desktop_general" class="content">
                <div id="nav">
                    <span class="content">
                        <span class="dir">Home</span>
                        <span class="separator">&gt;</span>
                    </span>
                </div>
                <div id="leftPanel">
                    <div id="listTypes">
                        <p><img src="./public/pictures/desktop/list/list.svg" /></p>
                        <p><img src="./public/pictures/desktop/list/grid.svg" /></p>
                        <p><img src="./public/pictures/desktop/list/atomic.svg" /></p>
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