<?php
    /*
	* @name            : vLogin.php
	* @description     : Login view
	* @authors         : Romain Claveau <romain.claveau@protonmail.ch>, Dylan Clement <dylanclement7@protonmail.ch>
	*/

    $_t = new Template("Connexion");
    $_t->addCss("Login/home_login");
    $_t->addJs("Login/log_connect");
    $_t->addJs("Login/sha512");
    $_t->getHeader();
?>

        <section id="header">
            <div id="logo"><img src="<?php echo MVC_ROOT; ?>/public/pictures/login/logo_anime.svg" /></div>
        </section>

        <section id="content">
            <div id="back"><p><a href="../photon">RETOUR &Agrave; QUANTACLOUD</a></p></div>

            <div id="avatar"><p><img src="<?php echo MVC_ROOT; ?>/public/pictures/login/user.svg" /></p></div>
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
	$_t->getFooter();
?>
