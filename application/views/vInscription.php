<?php
	/*
	* @nom             : register.php
	* @description     : Structure pour la page d'inscription
	* @authors         : Romain Claveau <romain.claveau@protonmail.ch>
	*/
	$_template = new Template("Inscription");
    $_template->addCss("Register/home_register");
    $_template->addJs("Register/sha512");
    $_template->addJs("Register/openpgp.min");
    $_template->addJs("Register/aes");
    $_template->addJs("Register/log_register");
   	$_template->getHeader();
?>
        <section id="header">
            <div id="logo"><img src="./public/pictures/register/logo_anime.svg" /></div>
        </section>
    
        <section id="content">
            <div id="back"><p><a href="../photon/">RETOUR &Agrave; QUANTACLOUD</a></p></div>
            
            <div id="avatar"><p><img src="./public/pictures/register/user.svg" /></p></div>
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
   $_template->getFooter();
?>