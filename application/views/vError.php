<?php
    /*
	* @name            : vError.php
	* @description     : Error view
	* @authors         : Romain Claveau <romain.claveau@protonmail.ch>, Dylan Clement <dylanclement7@protonmail.ch>
	*/

    $_t = new Template("Error");
    $_t->addCss("home_global");
    $_t->getHeader();
?>
<body>
        <section id="language">
            <div>
                <?php $this->getLanguageSelector(); ?>
            </div>
        </section>
    
        <section id="header">
            <div id="logo"><img src="<?php echo MVC_ROOT; ?>/public/pictures/login/logo_anime.svg" /></div>
        </section>
    
        <section id="content">
            <div id="back"><p><a href="javascript:history.go(-1);"><?php echo $this->txt->Global->back; ?></a></p></div>
            
            <div id="return">
                <p class="error"><?php echo $this->_error; ?></p>
            </div>
        </section>
</body>
<?php 
	$_t->getFooter();
?>