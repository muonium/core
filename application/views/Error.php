<?php
    $_template = new Template("Erreur","Login/home_login","Login/log_connect");
    $_template->getHeader();
?>

    <body>
        <section id="header">
            <div id="logo"><img src="./public/pictures/login/logo_anime.svg" /></div>
        </section>
    
        <section id="content">
            <div id="back"><p><a href="javascript:history.go(-1);">RETOUR</a></p></div>
            
            <div id="return">
                <p class="error">Une erreur s'est produite, la page specifiee est introuvable.</p>
            </div>
        </section>
    </body>
<?php 
	$_template->getFooter();
?>