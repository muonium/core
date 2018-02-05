<?php
	/* Register page */
    use \library\MVC as l;
	$_t = new l\Template(self::$txt->Global->register);

	$_t->addCss([
		'2018/style'
	])->addJS([
		'src/crypto/sjcl',
    	'base64',
    	'sha512',
    	'mui_hash',
    	'Register/log_register'
	]);

	echo $_t->getHead();
	echo $_t->getHeader();
?>
	<div class="container-small">

        <form class="form-register" action="" method="post">
            <h1><?php echo self::$txt->Global->register; ?></h1>

            <p class="input-large">
				<input type="text" id="field_mail" placeholder="<?php echo self::$txt->Register->email; ?>" required autofocus>
				<label class="fa fa-envelope" for="field_mail" aria-hidden="true"></label>
			</p>

			<p class="input-large">
				<input type="text" id="field_login" placeholder="<?php echo self::$txt->Register->login; ?>" required>
				<label class="fa fa-user" for="field_login" aria-hidden="true"></label>
			</p>

			<p class="input-large">
				<input type="password" id="field_pass" placeholder="<?php echo self::$txt->Register->password; ?>" required>
				<label class="fa fa-lock" for="field_pass" aria-hidden="true"></label>
			</p>

			<p class="input-large">
				<input type="password" id="field_pass_confirm" placeholder="<?php echo self::$txt->Register->confirm; ?>" required>
				<label class="fa fa-lock" for="field_pass_confirm" aria-hidden="true"></label>
			</p>

			<p class="input-large">
				<input type="password" id="field_passphrase" placeholder="<?php echo self::$txt->Register->passphrase; ?>" required>
				<label class="fa fa-lock" for="field_passphrase" aria-hidden="true"></label>
			</p>

			<p class="input-large">
				<input type="password" id="field_passphrase_confirm" placeholder="<?php echo self::$txt->Register->confirm; ?>" required>
				<label class="fa fa-lock" for="field_passphrase_confirm" aria-hidden="true"></label>
			</p>

			<fieldset class="nomargin">
				<legend><?php echo self::$txt->Profile->doubleAuth; ?></legend>
	            <p class="input-large">
					<input type="checkbox" id="doubleAuth" name="doubleAuth"> <label for="doubleAuth"><?php echo self::$txt->Register->doubleAuth; ?></label>
				</p>
			</fieldset>

			<div class="bloc-links">
				<a href="<?php echo MVC_ROOT; ?>/Login" class="mono blue"><?php echo self::$txt->Register->alreadyregistered; ?></a>
                <input type="button" class="btn btn-required" onclick="sendRegisterRequest(event)" value="<?php echo self::$txt->Global->register; ?>" disabled>
            </div>

            <div id="return">
				<p class="error"></p>
			</div>
        </form>
	</div>
<?php
   echo $_t->getFooter();
?>
