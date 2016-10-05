<?php
	/*
	* @name            : vRegister.php
	* @description     : Register view
	* @authors         : Romain Claveau <romain.claveau@protonmail.ch>, Dylan Clement <dylanclement7@protonmail.ch>
	*/
    use \library\MVC as l;
	$_t = new l\Template($this->txt->Global->register);
    $_t->addCss("home_global");
    $_t->addCss("Register/home_register");
    $_t->addJs("Register/sha512");
    $_t->addJs("Register/log_register");
   	$_t->getHeader();
?>
<body>
        <section id="language">
            <div>
                <?php $this->getLanguageSelector(); ?>
            </div>
        </section>

        <section id="header">
            <div id="logo"><img src="<?php echo MVC_ROOT; ?>/public/pictures/register/logo_anime.svg" /></div>
        </section>

        <section id="content">
            <div id="back"><p><a href="https://muonium.ch/photon/"><?php echo_h($this->txt->Global->back); ?></a></p></div>

            <div id="avatar"><p><img src="<?php echo MVC_ROOT; ?>/public/pictures/register/user.svg" /></p></div>
            <div id="text"><p><?php echo $this->txt->Global->register; ?></p></div>

            <div id="form">
                <input type="text" id="field_mail" placeholder="<?php echo_h($this->txt->Register->email); ?>..." autofocus><br />
                <input type="text" id="field_login" placeholder="<?php echo_h($this->txt->Register->login); ?>..." /><br />
                <input type="password" id="field_pass" placeholder="<?php echo_h($this->txt->Register->password); ?>..." /><br />
                <input type="password" id="field_pass_confirm" placeholder="<?php echo_h($this->txt->Register->confirm); ?>..." /><br />
                <input type="password" id="field_passphrase" placeholder="<?php echo_h($this->txt->Register->passphrase); ?>..."/><br />
                <input type="password" id="field_passphrase_confirm" placeholder="<?php echo_h($this->txt->Register->confirm); ?>..."/><br />
                <?php echo_h($this->txt->Register->doubleAuth); ?>
                <input type="checkbox" id="doubleAuth" name="doubleAuth"><br /><br />
                <a href="<?php echo MVC_ROOT; ?>/Login"><?php echo_h($this->txt->Register->alreadyregistered); ?></a><br />
                <input type="submit" value="<?php echo_h($this->txt->Global->register); ?>" onclick="sendRegisterRequest()"/>
            </div>

            <div id="return">
                <p class="error"><?php echo_h($this->txt->Register->impossible); ?> :)</p>
            </div>
        </section>
</body>
<?php
   $_t->getFooter();
?>
