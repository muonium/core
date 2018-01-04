<?php
	/* Validate view */
    use \library\MVC as l;
	$_t = new l\Template(self::$txt->Global->validate);

	$_t->addCss([
		'blue/blue',
	    'blue/container',
	    'blue/header',
	    'blue/inputs',
	    'blue/menu',
	    'blue/section-large-content'
	]);

	echo $_t->getHead();
	echo $_t->getHeader();
?>
	<div id="container">
        <section id="large-content">
            <h1><?php echo self::$txt->Global->validate; ?></h1>

            <p class="space">
                <?php echo_h($this->err_msg); ?><br>
                <a href="Login"><?php echo self::$txt->Global->login; ?></a> ||
                <a href="<?php echo $_SERVER['REQUEST_URI']; ?>"><?php echo self::$txt->Global->refresh; ?></a>
            </p>
        </section>
	</div>
<?php
   echo $_t->getFooter();
?>
