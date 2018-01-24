<?php
	/* Lost pass form */
    use \library\MVC as l;
	$_t = new l\Template(self::$txt->Login->forgot);

	$_t->addCss([
		'2018/style'
	])->addJs([
		'base64',
	    'sha512',
	    'mui_hash',
	    'LostPass/lostpass'
	]);

	echo $_t->getHead();
	echo $_t->getHeader();
?>
	<div class="container-small">
        <form>
            <h1><?php echo self::$txt->Login->forgot; ?></h1>

			<p><?php echo_h($this->err_msg); ?></p>

            <strong><?php echo self::$txt->Profile->changepwd; ?></strong>

            <p class="input-large">
                <input type="password" name="pwd" id="pwd" placeholder="<?php echo self::$txt->Profile->newpwd; ?>" required autofocus>
				<label class="fa fa-lock" for="pwd" aria-hidden="true"></label>
			</p>

            <p class="input-large">
                <input type="password" name="pwd_confirm" id="pwd_confirm" placeholder="<?php echo self::$txt->Register->confirm; ?>" required>
				<label class="fa fa-lock" for="pwd_confirm" aria-hidden="true"></label>
            </p>

            <br><input type="button" onclick="changePass()" class="btn btn-required" value="<?php echo self::$txt->Global->submit; ?>" disabled>

			<div id="returnArea"></div>
        </form>
	</div>
<?php
   echo $_t->getFooter();
?>
