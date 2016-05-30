<?php
    $_t = new Template("Erreur");
    $_t->addCss("home_login");
    $_t->getHeader();
?>

    <body>
        <section id="header">
            <div id="logo"><img src="./public/pictures/login/logo_anime.svg" /></div>
        </section>
    
        <section id="content">
            <div id="back"><p><a href="javascript:history.go(-1);">RETOUR</a></p></div>
            
            <div id="return">
                <p class="error"><?php echo $this->_error; ?></p>
            </div>
        </section>
    </body>
<?php 
	$_t->getFooter();
?>