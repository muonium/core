<?php
    /*
	* @name            : vError.php
	* @description     : Error view
	* @authors         : Romain Claveau <romain.claveau@protonmail.ch>, Dylan Clement <dylanclement7@protonmail.ch>
	*/
    use \library\MVC as l;
    $_t = new l\Template("Error");
    $_t->addCss("fonts/roboto");
    $_t->addCss("blue/blue");
    $_t->addCss("blue/container");
    $_t->addCss("blue/header");
    $_t->addCss("blue/inputs");
    $_t->addCss("blue/menu");
    $_t->addCss("blue/section-large-content");
    $_t->getHeader();
?>
<body class="grey">
    <header>
        <div id="logo"><img src="public/pictures/logos/muonium_H_06.png"></div>
        <ul>
            <li><a href="https://muonium.ch/photon/"><?php echo $this->txt->Global->back; ?></a></li>
        </ul>
        <section id="language">
            <div>
                <?php $this->getLanguageSelector(); ?>
            </div>
        </section>
    </header>

    <div id="container">
        <section id="large-content">
            <div id="back"><p><a href="javascript:history.go(-1);"><?php echo_h($this->txt->Global->back); ?></a></p></div>

            <div id="return">
                <p class="error"><?php echo $this->_error; ?></p>
            </div>
        </section>
    </div>
</body>
<?php
	$_t->getFooter();
?>
