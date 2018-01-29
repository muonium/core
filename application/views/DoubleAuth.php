<?php
	/* Double authentication */
    use \library\MVC as l;
    $_t = new l\Template(self::$txt->Global->login);

    $_t->addCss([
		'2018/style'
	])->addJs('check');

	echo $_t->getHead();
	echo $_t->getHeader();
?>
	<div class="container-small">
		<form method="post" action="<?php echo MVC_ROOT; ?>/Login/AuthCode">
        	<h1><?php echo self::$txt->Global->login; ?></h1>

			<p class="mtop"><?php if(!empty($this->_message)) { echo $this->_message; } ?></p>
			<p class="input-large">
                <input type="text" name="code" class="noicon" placeholder="<?php echo self::$txt->Login->codeMail; ?>" required>
			</p>
            <input type="submit" class="btn" value="<?php echo self::$txt->Global->submit; ?>">
        </form>
    </div>
<?php
	echo $_t->getFooter();
?>
