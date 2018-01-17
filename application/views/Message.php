<?php
	/* View with a message defined in the controller */
    use \library\MVC as l;
	$_t = new l\Template(self::$txt->Global->validate);

	$_t->addCss([
		'2018/style'
	]);

	echo $_t->getHead();
	echo $_t->getHeader();
?>
	<div class="container-small">
        <p>
            <?php if(isset($this->_message)) { echo_h($this->_message); } ?>
        </p>
	</div>
<?php
   echo $_t->getFooter();
?>
