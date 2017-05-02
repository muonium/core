<?php
/*
	* @name            : vLogin.php
	* @description     : Login view
	* @authors         : Romain Claveau <romain.claveau@protonmail.ch>, Dylan Clement <dylanclement7@protonmail.ch>
*/
    use \library\MVC as l;
    $_t = new l\Template($this->txt->Global->login);
    $_t->addCss("blue/blue");
    $_t->addCss("blue/container");
    $_t->addCss("blue/header");
    $_t->addCss("blue/inputs");
    $_t->addCss("blue/menu");
    $_t->addCss("blue/section-large-content");

    $_t->addJs("base64");
	$_t->addJs("src/crypto/sjcl");
    $_t->addJs("sha512");
    $_t->addJs("mui_hash");
    $_t->addJs("Login/log_connect");
    $_t->getHeader();
?>
<body class="grey">
    <header>
        <div id="logo">
            <a href="https://www.muonium.ch" target="_blank">
                <img src="public/pictures/logos/muonium_H_06.png">
            </a>
        </div>
        <section id="language">
            <div>
                <?php $this->getLanguageSelector(); ?>
            </div>
        </section>
    </header>

    <div id="container">
        <section id="large-content" class="spaced">
            <h2><?php echo_h($this->txt->Global->login); ?></h2>

            <div id="form">
                <p style="color:red"><?php if(!empty($this->_message)) { echo $this->_message; } ?></p>

                <p>
                    <label class="fa fa-user" for="field_username" aria-hidden="true"></label><!--
                    --><input type="text" name="username" id="field_username" placeholder="<?php echo_h($this->txt->Login->username); ?>..." required="required" autofocus>
                </p>

                <p>
                    <label class="fa fa-key" for="field_password" aria-hidden="true"></label><!--
                    --><input type="password" name="pass" id="field_password" placeholder="<?php echo_h($this->txt->Register->password); ?>..." required="required" />
                </p>

                <p>
                    <label class="fa fa-key" for="field_passphrase" aria-hidden="true"></label><!--
                    --><input type="password" name="passphrase" id="field_passphrase" placeholder="<?php echo_h($this->txt->Register->passphrase); ?>..." required="required" />
                </p>

                <a href="<?php echo MVC_ROOT; ?>/LostPass"><?php echo_h($this->txt->Login->forgot); ?></a> &nbsp;&nbsp;
                <a href="<?php echo MVC_ROOT; ?>/Register"><?php echo_h($this->txt->Login->register); ?></a><br />

                <input type="submit" value="<?php echo_h($this->txt->Global->login); ?>" onclick="sendConnectionRequest()"/>
            </div>

            <div id="return">
                <p class="error"></p>
            </div>
        </section>
    </div>
</body>
<?php
$_t->getFooter();
?>
