<?php
    /* Error page */
    use \library\MVC as l;
    $_t = new l\Template("Error");

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
            <div id="return">
                <p class="error"><?php echo $this->_error; ?></p>
            </div>
        </section>
    </div>
<?php
	echo $_t->getFooter();
?>
