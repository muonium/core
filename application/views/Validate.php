<?php
	/*
	* @name            : Validate.php
	* @description     : Validate view
	* @authors         : Dylan Clement <dylanclement7@protonmail.ch>
	*/
    use \library\MVC as l;
	$_t = new l\Template($this->txt->Global->validate);
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
                <img src="public/pictures/logos/muonium_H_06.png" title="<?php echo $this->txt->Global->home; ?>" alt="<?php echo $this->txt->Global->home; ?>">
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
            <h2><?php echo_h($this->txt->Global->validate); ?></h2>

			<br /><br />
            <p>
                <?php echo_h($this->err_msg); ?><br />
                <a href="Login"><?php echo_h($this->txt->Global->login); ?></a> ||
                <a href="<?php echo $_SERVER['REQUEST_URI']; ?>"><?php echo_h($this->txt->Global->refresh); ?></a>
            </p>
        </section>
	</div>
</body>
<?php
   $_t->getFooter();
?>
