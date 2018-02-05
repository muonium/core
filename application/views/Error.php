<?php
    /* Error page */
    use \library\MVC as l;
    $_t = new l\Template("Error");

    $_t->addCss([
		'2018/style'
	]);

	echo $_t->getHead();
	echo $_t->getHeader();
?>
    <div class="container-small">
        <div id="return">
            <p class="error"><?php echo $this->_error; ?></p>
        </div>
    </div>
<?php
	echo $_t->getFooter();
?>
