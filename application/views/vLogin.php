<?php
    $_template = new Template("Connexion");
    $_template->addCss("Login/home_login");
    $_template->addJs("Login/log_connect");
    $_template->addJs("Login/sha512");
    $_template->addJs("Login/openpgp.min");
    $_template->addJs("Login/aes");
    $_template->getHeader();
?>

        <section id="header">
            <div id="logo"><img src="./public/pictures/login/logo_anime.svg" /></div>
        </section>
    
        <section id="content">
            <div id="back"><p><a href="../photon">RETOUR &Agrave; QUANTACLOUD</a></p></div>
            
            <div id="avatar"><p><img src="./public/pictures/login/user.svg" /></p></div>
            <div id="text"><p>Connexion</p></div>
            
            <div id="form">
                <input type="text" id="field_mail" placeholder="Adresse mail..."  /><br />
                <input type="password" id="field_password" placeholder="Mot de passe..." /><br />
                <input type="password" id="field_passphrase" placeholder="PassPhrase..." /><br /><br />
                <a href="#">Mot de passe oublié ?</a>&nbsp;<a href="#">Déjà inscrit ?</a><br />
                <input type="submit" value="Se connecter" onclick="sendConnectionRequest()"/>
            </div>
            
            <div id="return">
                <p class="error">L'interface n'est pas encore prête :)</p>
            </div>
        </section>
<?php 
	$_template->getFooter();
?>