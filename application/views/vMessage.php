<?php
	/*
	* @name            : vMessage.php
	* @description     : View with a message defined in controller
	* @authors         : Dylan Clement <dylanclement7@protonmail.ch>
	*/
    use \library\MVC as l;
	$_t = new l\Template($this->txt->Global->validate);
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
            <p>
                <?php
                if(!empty($this->_message)) { echo_h($this->_message); }
                ?>
                <br />
            </p>
        </section>
	</div>
</body>
<?php
   $_t->getFooter();
?>
