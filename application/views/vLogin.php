<?php
/*
	* @name            : vLogin.php
	* @description     : Login view
	* @authors         : Romain Claveau <romain.claveau@protonmail.ch>, Dylan Clement <dylanclement7@protonmail.ch>
*/
    use \library\MVC as l;
    $_t = new l\Template($this->txt->Global->login);
    $_t->addCss("home_global");
    $_t->addCss("Login/home_login");
    $_t->addJs("Login/log_connect");
    $_t->addJs("Login/sha512");
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
        <div id="back"><p><a href="https://muonium.ch/photon/"><?php echo $this->txt->Global->back; ?></a></p></div>

        <div id="avatar"><p><img src="<?php echo MVC_ROOT; ?>/public/pictures/login/user.svg" /></p></div>
        <div id="text"><p><?php echo_h($this->txt->Global->login); ?></p></div>

        <div id="form">
            <p style="color:red"><?php if(!empty($this->_message)) { echo $this->_message; } ?></p>
            <input type="text" name="mail" id="field_mail" placeholder="<?php echo_h($this->txt->Register->email); ?>..." required="required" /><br />
            <input type="password" name="pass" id="field_password" placeholder="<?php echo_h($this->txt->Register->password); ?>..." required="required" /><br />
            <input type="password" name="passphrase" id="field_passphrase" placeholder="<?php echo_h($this->txt->Register->passphrase); ?>..." required="required" /><br /><br />
            <a href="<?php echo MVC_ROOT; ?>/LostPass"><?php echo_h($this->txt->Login->forgot); ?></a> &nbsp;&nbsp; <a href="<?php echo MVC_ROOT; ?>/Register"><?php echo_h($this->txt->Login->register); ?></a><br />
            <input type="submit" value="<?php echo_h($this->txt->Global->login); ?>" onclick="sendConnectionRequest()"/>
        </div>

        <div id="return">
            <p class="error"><?php echo_h($this->txt->Global->notready); ?> :)</p>
        </div>
    </section>
</body>
<?php
$_t->getFooter();
?>
