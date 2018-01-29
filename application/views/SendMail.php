<?php
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

        <?php echo self::$txt->Validate->sendmess; ?>
        <a href="Validate/SendMail" class="block mtop"><?php echo self::$txt->Validate->sendmail; ?></a>
	</div>
<?php
   echo $_t->getFooter();
?>
