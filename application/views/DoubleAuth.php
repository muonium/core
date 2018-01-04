<?php
	/* Double authentication */
    use \library\MVC as l;
    $_t = new l\Template(self::$txt->Global->login);

    $_t->addCss([
		'blue/blue',
	    'blue/container',
	    'blue/header',
	    'blue/inputs',
	    'blue/menu',
	    'blue/section-large-content'
	])->addJs('check');

	echo $_t->getHead();
	echo $_t->getHeader();
?>
	<div id="container">
        <section id="large-content">
            <h2><?php echo self::$txt->Global->login; ?></h2>

            <div id="form">
                <form method="post" action="<?php echo MVC_ROOT; ?>/Login/AuthCode">
                    <p style="color:red"><?php if(!empty($this->_message)) { echo $this->_message; } ?></p>
                    <input type="text" name="code" placeholder="<?php echo self::$txt->Login->codeMail; ?>" required>
                    <input type="submit" value="<?php echo self::$txt->Global->submit; ?>">
                </form>
            </div>
        </section>
    </div>
<?php
	echo $_t->getFooter();
?>
