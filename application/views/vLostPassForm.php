<?php
	/*
	* @name            : vLostPassForm.php
	* @description     : Lost pass form view (password or passphrase)
	* @authors         : Dylan Clement <dylanclement7@protonmail.ch>
	*/
    use \library\MVC as l;
	$_t = new l\Template($this->txt->Login->forgot);
    $_t->addCss("home_global");
    $_t->addCss("Register/home_register");
    $_t->addJs("base64");
    $_t->addJs("sha512");
    $_t->addJs("mui_hash");
    $_t->addJs("LostPass/lostpass");
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
            <div id="back"><p><a href="http://muonium.ch/photon/"><?php echo_h($this->txt->Global->back); ?></a></p></div>

            <div id="avatar"><p><img src="<?php echo MVC_ROOT; ?>/public/pictures/register/user.svg" /></p></div>
            <div id="text"><p><?php echo_h($this->txt->Login->forgot); ?></p></div>

            <div>
                <?php echo_h($this->err_msg); ?><br />
                <div id="returnArea"></div>
                <fieldset>
                    <legend><?php echo_h($this->txt->Profile->changepwd); ?></legend>

                    <p><label for="pwd"><?php echo_h($this->txt->Profile->newpwd); ?></label>
                    <input type="password" name="pwd" id="pwd" autofocus></p>

                    <p><label for="pwd_confirm"><?php echo_h($this->txt->Register->confirm); ?></label>
                    <input type="password" name="pwd_confirm" id="pwd_confirm"></p>
                </fieldset>

                <fieldset>
                    <legend><?php echo_h($this->txt->Profile->changepp); ?></legend>
                    <p>
                        <?php echo str_replace("[count]", $this->ppCounter, $this->txt->Profile->warningpp); ?>
                         <?php if($this->ppCounter >= 2) { echo '<br /><strong>'.$this->txt->LostPass->reset.'</strong>';  } ?>
                    </p>
                    <p><label for="pp"><?php echo_h($this->txt->Profile->newpp); ?></label>
                    <input type="password" name="pp" id="pp"></p>

                    <p><label for="pp_confirm"><?php echo_h($this->txt->Register->confirm); ?></label>
                    <input type="password" name="pp_confirm" id="pp_confirm"></p>
                </fieldset>

                <input type="button" onclick="changePass()" value="<?php echo_h($this->txt->Global->submit); ?>">
            </div>
        </section>
</body>
<?php
   $_t->getFooter();
?>
