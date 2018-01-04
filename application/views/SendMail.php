<?php
	/* Sendmail view (just a link to sendMail method) */
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
                <?php echo self::$txt->Validate->sendmess; ?><br>
                <a href="SendMail"><?php echo self::$txt->Validate->sendmail; ?></a>
            </p>
        </section>
	</div>
<?php
   echo $_t->getFooter();
?>
