<?php
	/*
	* @name            : vValidate.php
	* @description     : Validate view
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
            <div id="back"><p><a href="../photon/"><?php echo $this->txt->Global->back; ?></a></p></div>

            <div id="avatar"><p><img src="<?php echo MVC_ROOT; ?>/public/pictures/register/user.svg" /></p></div>
            <div id="text"><p><?php echo $this->txt->Global->validate; ?></p></div>

            <p>
                <?php echo $this->err_msg; ?><br />
                <a href="<?php echo MVC_ROOT; ?>/Login/UserValidation"><?php echo $this->txt->Global->login; ?></a> || 
                <a href="<?php echo $_SERVER['REQUEST_URI']; ?>"><?php echo $this->txt->Global->refresh; ?></a>
            </p>
        </section>
</body>
<?php
   $_t->getFooter();
?>
