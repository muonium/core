<?php
	/*
	* @name            : vMessage.php
	* @description     : View with a message defined in controller
	* @authors         : Dylan Clement <dylanclement7@protonmail.ch>
	*/

	$_t = new Template($this->txt->Global->validate);
    $_t->addCss("home_global");
    $_t->addCss("Register/home_register");
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
            <div id="back"><p><a href="../photon/"><?php echo_h($this->txt->Global->back); ?></a></p></div>

            <div id="avatar"><p><img src="<?php echo MVC_ROOT; ?>/public/pictures/register/user.svg" /></p></div>

            <p>
                <?php 
                if(!empty($this->_message)) { echo_h($this->_message); }
                ?>
                <br />
            </p>
        </section>
</body>
<?php
   $_t->getFooter();
?>