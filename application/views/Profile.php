<?php
    /* Profile view (edit profile) */
    use \library\MVC as l;
    $_t = new l\Template(self::$txt->Global->profile);

    $_t->addCss([
		'2018/style'
	])->addJs([
		'check',
	    'Interface/interface',
	    'Interface/Request',
	    'Profile/profile',
	    'sha512',
		'mui_hash',
		'src/crypto/sjcl',
		'base64'
	]);

	echo $_t->getHead();
	echo $_t->getHeader();
	echo $_t->getSidebar();
?>
    <div class="container-large">
		<div class="info mono">
			<?php echo self::$txt->Profile->upgrade; ?> <a href="<?php echo MVC_ROOT; ?>/Upgrade"><?php echo self::$txt->Profile->getmore; ?></a>
		</div><br>

		<h1><?php echo self::$txt->UserMenu->settings; ?></h1>
		<fieldset>
			<legend><?php echo self::$txt->Global->profile; ?></legend>

	        <p>
				<span class="label"><?php echo self::$txt->Register->login.':</span>
				<span id="username">'.htmlentities($_SESSION['login']); ?></span>
			</p>
			<p>
				<span class="label"><?php echo self::$txt->Register->email.':</span>
				<span id="email">'.htmlentities($this->_modelUser->getEmail()); ?></span>
			</p>
			<p>
				<span class="label">ID:</span>
				<?php echo $_SESSION['id']; ?>
			</p>
		</fieldset>

        <fieldset>
            <legend><?php echo self::$txt->Profile->mailusername; ?></legend>
			<div class="bloc-input">
				<div>
					<form>
						<h3 class="nowrap"><?php echo self::$txt->Profile->changelogin; ?></h3>
			            <p class="input-large">
							<input type="text" name="new_login" id="new_login" placeholder="<?php echo self::$txt->Profile->newlogin; ?>" required>
			                <label class="fa fa-user" for="new_login" aria-hidden="true"></label>
			            </p>
			            <input type="submit" class="btn btn-required btn-profile" onclick="changeLogin(event)" value="<?php echo self::$txt->Global->submit; ?>" disabled>
			            <div id="changeLoginReturn"></div>
					</form>
				</div>
				<div>
					<form>
						<h3 class="nowrap"><?php echo self::$txt->Profile->changemail; ?></h3>
			            <p class="input-large">
			                <input type="text" name="new_mail" id="new_mail" placeholder="<?php echo self::$txt->Profile->changemail; ?>" required>
							<label class="fa fa-envelope" for="new_mail" aria-hidden="true"></label>
			            </p>
			            <input type="submit" class="btn btn-required btn-profile" onclick="changeMail(event)" value="<?php echo self::$txt->Global->submit; ?>" disabled>
			            <div id="changeMailReturn"></div>
					</form>
				</div>
			</div>
        </fieldset>

        <fieldset>
            <legend><?php echo self::$txt->Profile->pwdpp; ?></legend>
			<div class="bloc-input">
				<div>
					<form>
						<h3><?php echo self::$txt->Profile->changepwd; ?></h3>
		                <p class="input-large">
							<input type="password" name="old_pwd" id="old_pwd" placeholder="<?php echo self::$txt->Profile->oldpwd; ?>" required>
		                    <label class="fa fa-lock" for="old_pwd" aria-hidden="true"></label>
		                </p>
						<p class="input-large">
							<input type="password" name="new_pwd" id="new_pwd" placeholder="<?php echo self::$txt->Profile->newpwd; ?>" required>
		                    <label class="fa fa-lock" for="new_pwd" aria-hidden="true"></label>
		                </p>
						<p class="input-large">
							<input type="password" name="pwd_confirm" id="pwd_confirm" placeholder="<?php echo self::$txt->Register->confirm; ?>" required>
		                    <label class="fa fa-lock" for="pwd_confirm" aria-hidden="true"></label>
		                </p>
	                	<input type="submit" class="btn btn-required btn-profile" onclick="changePassword(event)" value="<?php echo self::$txt->Global->submit; ?>" disabled>
	                	<div id="changePasswordReturn"></div>
					</form>
				</div>
				<div>
					<form>
		                <h3><?php echo self::$txt->Profile->changepp; ?></h3>
		                <p class="input-large">
							<input type="password" name="old_pp" id="old_pp" placeholder="<?php echo self::$txt->Profile->oldpp; ?>" required>
		                    <label class="fa fa-lock" for="old_pp" aria-hidden="true"></label>
		                </p>
						<p class="input-large">
							<input type="password" name="new_pp" id="new_pp" placeholder="<?php echo self::$txt->Profile->newpp; ?>" required>
		                    <label class="fa fa-lock" for="new_pp" aria-hidden="true"></label>
		                </p>
						<p class="input-large">
							<input type="password" name="pp_confirm" id="pp_confirm" placeholder="<?php echo self::$txt->Register->confirm; ?>" required>
		                    <label class="fa fa-lock" for="pp_confirm" aria-hidden="true"></label>
		                </p>
		                <input type="submit" class="btn btn-required btn-profile" onclick="changeCek(event)" value="<?php echo self::$txt->Global->submit; ?>" disabled>
		                <div id="changePassPhraseReturn"></div>
					</form>
				</div>
            </div>
		</fieldset>

        <fieldset>
            <legend><?php echo self::$txt->Profile->otheroptions; ?></legend>
			<h3><?php echo self::$txt->Profile->theme; ?></h3>
            <p class="input-large">
                <input type="radio" name="theme" id="light" onclick="switchTheme()">
				<label for="light">Light</label>
				<input type="radio" name="theme" id="dark" onclick="switchTheme()">
				<label for="dark">Dark</label>
            </p>
        </fieldset>

        <fieldset>
            <h3><?php echo self::$txt->Profile->doubleAuth; ?></h3>
            <p class="input-large">
                <input type="checkbox" name="doubleAuth" onclick="changeAuth()" id="doubleAuth"<?php if($this->_modelUser->getDoubleAuth()) { echo ' checked'; } ?>>
                <label for="doubleAuth"><?php echo self::$txt->Register->doubleAuth; ?></label>
            </p>
            <div id="changeAuthReturn"></div>
        </fieldset>

		<fieldset>
			<form>
				<h3><?php echo self::$txt->Profile->deleteAccount; ?></h3>
				<p class="input-large">
	                <input type="checkbox" name="delete" id="delete" required>
	                <label for="delete"><?php echo self::$txt->Profile->iwant; ?></label>
	            </p>
				<input type="submit" class="btn btn-required btn-warning" onclick="ConfirmDelete(event)" value="<?php echo self::$txt->Profile->deleteAccount; ?>" disabled>
				<div id="deleteUserReturn"></div>
			</form>
		</fieldset>
    </div>
<?php
    echo $_t->getFooter();
?>
