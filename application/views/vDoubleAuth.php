<?php
/*
	* @name            : vDoubleAuth.php
	* @description     : Double auth view
	* @authors         : Dylan Clement <dylanclement7@protonmail.ch>
*/
    use \library\MVC as l;
    $_t = new l\Template($this->txt->Global->login);
    $_t->addCss("home_global");
    $_t->addCss("Login/home_login");
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
            <form method="post" action="<?php echo MVC_ROOT; ?>/Login/AuthCode">
                <p style="color:red"><?php if(!empty($this->_message)) { echo $this->_message; } ?></p>
                <input type="text" name="code" placeholder="<?php echo_h($this->txt->Login->codeMail); ?>" required>
                <input type="submit">
            </form>
        </div>
    </section>
</body>
<?php
$_t->getFooter();
?>
