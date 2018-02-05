<?php
	/* Lost pass */
    use \library\MVC as l;
	$_t = new l\Template(self::$txt->Login->forgot);

	$_t->addCss([
		'2018/style'
	]);

	echo $_t->getHead();
	echo $_t->getHeader();
?>
	<div class="container-small">
        <form method="post" action="<?php echo MVC_ROOT; ?>/LostPass/SendMail">
            <h1><?php echo self::$txt->Login->forgot; ?></h1>

            <p><?php echo $this->err_msg; ?></p>

			<p class="input-large">
            	<input type="text" name="user" id="user" placeholder="<?php echo self::$txt->LostPass->user; ?>" value="<?php if(isset($_POST['user'])) { echo_h($_POST['user']); } ?>" required>
				<label class="fa fa-user" for="user" aria-hidden="true"></label>
			</p>

			<div class="bloc-links">
				<a href="<?php echo MVC_ROOT; ?>/Login" class="mono blue"><?php echo self::$txt->Login->signin; ?></a>
                <input type="submit" class="btn btn-required" value="<?php echo self::$txt->Global->submit; ?>" disabled>
            </div>
        </form>
	</div>
<?php
   echo $_t->getFooter();
?>
