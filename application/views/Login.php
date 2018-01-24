<?php
	/* Login page */
    use \library\MVC as l;
    $_t = new l\Template(self::$txt->Global->login);

    $_t->addCss([
		'2018/style'
	])->addJs([
		'base64',
		'src/crypto/sjcl',
		'sha512',
		'mui_hash',
		'Login/log_connect'
	]);

    echo $_t->getHead();
	echo $_t->getHeader();
?>
    <div class="container-small">

        <form class="form-login" action="" method="post">
			<h1><?php echo self::$txt->Global->login; ?></h1>
            <p class="red"><?php if(isset($this->_message)) { echo $this->_message; } ?></p>

			<p class="input-large">
                <input type="text" name="username" id="field_username" placeholder="<?php echo self::$txt->Login->username; ?>" required autofocus>
				<label class="fa fa-user" for="field_username" aria-hidden="true"></label>
			</p>

			<p class="input-large">
            	<input type="password" name="pass" id="field_password" placeholder="<?php echo self::$txt->Register->password; ?>" required>
				<label class="fa fa-lock" for="field_password" aria-hidden="true"></label>
			</p>

			<p class="input-large">
            	<input type="password" name="passphrase" id="field_passphrase" placeholder="<?php echo self::$txt->Register->passphrase; ?>" required>
				<label class="fa fa-lock" for="field_passphrase" aria-hidden="true"></label>
			</p>

			<div class="bloc-links">
	            <a href="<?php echo MVC_ROOT; ?>/LostPass" class="mono blue"><?php echo self::$txt->Login->forgot; ?></a>
				<input type="button" class="btn btn-required" onclick="sendConnectionRequest(event)" value="<?php echo self::$txt->Login->signin; ?>" disabled>
			</div>

			<a href="<?php echo MVC_ROOT; ?>/Register" class="mono center"><?php echo self::$txt->Login->register; ?></a>

            <div id="return">
                <p class="error"></p>
            </div>
		</form>
    </div>
<?php
	echo $_t->getFooter();
?>
