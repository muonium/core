<?php
	/*
	* @name            : vRegister.php
	* @description     : Register view
	* @authors         : Romain Claveau <romain.claveau@protonmail.ch>, Dylan Clement <dylanclement7@protonmail.ch>
	*/

	$_t = new Template("Inscription");
    $_t->addCss("Register/home_register");
    $_t->addJs("Register/sha512");
    $_t->addJs("Register/log_register")
   	$_t->getHeader();
?>
        <section id="header">
            <div id="logo"><img src="<?php echo MVC_ROOT; ?>/public/pictures/register/logo_anime.svg" /></div>
        </section>

        <section id="content">
            <div id="back"><p><a href="../photon/">RETOUR &Agrave; QUANTACLOUD</a></p></div>

            <div id="avatar"><p><img src="<?php echo MVC_ROOT; ?>/public/pictures/register/user.svg" /></p></div>
            <div id="text"><p>Inscription</p></div>

            <div id="form">
                <input type="text" id="field_mail" placeholder="Adresse mail..." /><br />
                <input type="text" id="field_pseudo" placeholder="Pseudo..." /><br />
                <input type="password" id="field_pass" placeholder="Mot de passe..." /><br />
                <input type="password" id="field_pass_confirm" placeholder="Confirmez..." /><br />
                <input type="password" id="field_passphrase" placeholder="PassPhrase..."/><br />
                <input type="password" id="field_passphrase_confirm" placeholder="Confirmez..."/><br /><br />
                <a href="Connexion">Déjà inscrit ?</a><br />
                <input type="submit" value="S'inscrire" onclick="sendRegisterRequest()"/>
            </div>

            <div id="return">
                <p class="error">Inscriptions impossibles pour le moment :)</p>
            </div>
        </section>
<?php
   $_t->getFooter();
?>
