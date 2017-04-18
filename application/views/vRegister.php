<?php
	/*
	* @name            : vRegister.php
	* @description     : Register view
	* @authors         : Romain Claveau <romain.claveau@protonmail.ch>, Dylan Clement <dylanclement7@protonmail.ch>
	*/
    use \library\MVC as l;
	$_t = new l\Template($this->txt->Global->register);
	$_t->addCss("fonts/roboto");
	$_t->addCss("blue/blue");
    $_t->addCss("blue/container");
    $_t->addCss("blue/header");
    $_t->addCss("blue/inputs");
    $_t->addCss("blue/menu");
    $_t->addCss("blue/section-large-content");

	$_t->addJS("src/crypto/sjcl");
    $_t->addJs("base64");
    $_t->addJs("sha512");
    $_t->addJs("mui_hash");
    $_t->addJs("Register/log_register");
   	$_t->getHeader();
?>
<body class="grey">
	<header>
        <div id="logo"><img src="public/pictures/logos/muonium_H_06.png"></div>
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
            <h2><?php echo $this->txt->Global->register; ?></h2>

            <div id="form">
                <p>
					<label class="fa fa-envelope" for="field_mail" aria-hidden="true"></label><!--
                    --><input type="text" id="field_mail" placeholder="<?php echo_h($this->txt->Register->email); ?>..." autofocus>
				</p>

				<p>
					<label class="fa fa-user" for="field_login" aria-hidden="true"></label><!--
                    --><input type="text" id="field_login" placeholder="<?php echo_h($this->txt->Register->login); ?>..." />
				</p>

				<p>
					<label class="fa fa-key" for="field_pass" aria-hidden="true"></label><!--
                    --><input type="password" id="field_pass" placeholder="<?php echo_h($this->txt->Register->password); ?>..." />
				</p>

				<p>
					<label class="fa fa-key" for="field_pass_confirm" aria-hidden="true"></label><!--
                    --><input type="password" id="field_pass_confirm" placeholder="<?php echo_h($this->txt->Register->confirm); ?>..." />
				</p>

				<p>
					<label class="fa fa-key" for="field_passphrase" aria-hidden="true"></label><!--
                    --><input type="password" id="field_passphrase" placeholder="<?php echo_h($this->txt->Register->passphrase); ?>..."/>
				</p>

				<p>
					<label class="fa fa-key" for="field_passphrase_confirm" aria-hidden="true"></label><!--
                    --><input type="password" id="field_passphrase_confirm" placeholder="<?php echo_h($this->txt->Register->confirm); ?>..."/>
				</p>

                <p>
					<input type="checkbox" id="doubleAuth" name="doubleAuth"> <label for="doubleAuth"><?php echo_h($this->txt->Register->doubleAuth); ?></label>
                	&nbsp;&nbsp;<a href="<?php echo MVC_ROOT; ?>/Login"><?php echo_h($this->txt->Register->alreadyregistered); ?></a>
				</p>

                <input type="submit" value="<?php echo_h($this->txt->Global->register); ?>" onclick="sendRegisterRequest()"/>
            </div>

            <div id="return">
                <p class="error"><?php //echo_h($this->txt->Register->impossible); ?></p>
            </div>
        </section>
	</div>
</body>
<?php
   $_t->getFooter();
?>
