<?php
	/* Validate view */
    use \library\MVC as l;
	$_t = new l\Template(self::$txt->Global->validate);

	$_t->addCss([
		'2018/style'
	]);

	echo $_t->getHead();
	echo $_t->getHeader();
?>
	<div class="container-small">
        <h1><?php echo self::$txt->Global->validate; ?></h1>

        <?php echo_h($this->err_msg); ?><br>
        <a href="<?php echo $_SERVER['REQUEST_URI']; ?>"><?php echo self::$txt->Global->refresh; ?></a>
	</div>
<?php
   echo $_t->getFooter();
?>
