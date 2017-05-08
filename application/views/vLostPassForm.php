<?php
	/*
	* @name            : vLostPassForm.php
	* @description     : Lost pass form view (password or passphrase)
	* @authors         : Dylan Clement <dylanclement7@protonmail.ch>
	*/
    use \library\MVC as l;
	$_t = new l\Template($this->txt->Login->forgot);
	$_t->addCss("blue/blue");
    $_t->addCss("blue/container");
    $_t->addCss("blue/header");
    $_t->addCss("blue/inputs");
    $_t->addCss("blue/menu");
    $_t->addCss("blue/section-large-content");

    $_t->addJs("base64");
    $_t->addJs("sha512");
    $_t->addJs("mui_hash");
    $_t->addJs("LostPass/lostpass");
    $_t->getHeader();
?>
<body class="grey">
	<header>
		<div id="logo">
            <a href="https://muonium.io" target="_blank">
                <img src="public/pictures/logos/muonium_H_06.png">
            </a>
        </div>
        <ul>
            <li><a href="User"><?php echo $this->txt->Global->back; ?></a></li>
        </ul>
        <section id="language">
            <div>
                <?php $this->getLanguageSelector(); ?>
            </div>
        </section>
    </header>

	<div id="container">
        <section id="large-content">
            <h2><?php echo_h($this->txt->Login->forgot); ?></h2>

            <br /><br />
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

                <!--<fieldset>
                    <legend><?php /*echo_h($this->txt->Profile->changepp);*/ ?></legend>
                    <p>
                        <?php /*echo str_replace("[count]", $this->ppCounter, $this->txt->Profile->warningpp);*/ ?>
                         <?php /*if($this->ppCounter >= 2) { echo '<br /><strong>'.$this->txt->LostPass->reset.'</strong>';  }*/ ?>
                    </p>
                    <p><label for="pp"><?php /*echo_h($this->txt->Profile->newpp);*/ ?></label>
                    <input type="password" name="pp" id="pp"></p>

                    <p><label for="pp_confirm"><?php /*echo_h($this->txt->Register->confirm);*/ ?></label>
                    <input type="password" name="pp_confirm" id="pp_confirm"></p>
                </fieldset>-->

                <input type="button" onclick="changePass()" value="<?php echo_h($this->txt->Global->submit); ?>">
            </div>
        </section>
	</div>
</body>
<?php
   $_t->getFooter();
?>
