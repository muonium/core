<?php
	/*
	* @name            : SendMail.php
	* @description     : Sendmail view (just a link to sendMail method)
	* @authors         : Dylan Clement <dylan@muonium.ee>
	*/
    use \library\MVC as l;
	$_t = new l\Template(self::$txt->Global->validate);
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
		<div id="logo">
            <a href="https://muonium.io" target="_blank">
                <img src="public/pictures/logos/muonium_H_06.png" title="<?php echo self::$txt->Global->home; ?>" alt="<?php echo self::$txt->Global->home; ?>">
            </a>
        </div>
        <ul>
            <li><a href="User"><?php echo self::$txt->Global->back; ?></a></li>
        </ul>
        <section id="language">
            <div>
                <?php $this->getLanguageSelector(); ?>
            </div>
        </section>
    </header>

	<div id="container">
        <section id="large-content">
            <h1><?php echo_h(self::$txt->Global->validate); ?></h1>

            <p class="space">
                <?php echo_h(self::$txt->Validate->sendmess); ?><br>
                <a href="SendMail"><?php echo_h(self::$txt->Validate->sendmail); ?></a>
            </p>
        </section>
	</div>
</body>
<?php
   $_t->getFooter();
?>
