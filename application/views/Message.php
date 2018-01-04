<?php
	/* View with a message defined in the controller */
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
            <p>
                <?php
                if(!empty($this->_message)) { echo_h($this->_message); }
                ?>
                <br>
            </p>
        </section>
	</div>
<?php
   echo $_t->getFooter();
?>
