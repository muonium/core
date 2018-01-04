<?php
	/* Lost pass */
    use \library\MVC as l;
	$_t = new l\Template(self::$txt->Login->forgot);

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
            <h1><?php echo self::$txt->Login->forgot; ?></h1>

			<form method="post" action="<?php echo MVC_ROOT; ?>/LostPass/SendMail">
                <?php echo $this->err_msg; ?><br>
                <input type="text" name="user" id="user" placeholder="<?php echo self::$txt->LostPass->user; ?>" value="<?php if(!empty($_POST['user'])) { echo_h($_POST['user']); } ?>">
                <input type="submit" value="<?php echo self::$txt->Global->submit; ?>">
            </form>
        </section>
	</div>
<?php
   echo $_t->getFooter();
?>
