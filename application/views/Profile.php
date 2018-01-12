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
			<?php echo self::$txt->Profile->upgrade; ?> <a href="Upgrade"><?php echo self::$txt->Profile->getmore; ?></a>
		</div><br>

		<h1><?php echo self::$txt->UserMenu->settings; ?></h1>
		<fieldset>
			<legend><?php echo self::$txt->Global->profile; ?></legend>

	        <p>
				<span class="label"><?php echo self::$txt->Register->login.'&nbsp;:</span>
				'.htmlentities($_SESSION['login']); ?>
			</p>
			<p>
				<span class="label"><?php echo self::$txt->Register->email.'&nbsp;:</span>
				'.htmlentities($this->_modelUser->getEmail()); ?>
			</p>
			<p>
				<span class="label">ID&nbsp;:</span>
				<?php echo $_SESSION['id']; ?>
			</p>
		</fieldset>

        <fieldset>
            <legend><?php echo self::$txt->Profile->mailusername; ?></legend>
			<div class="bloc-input">
				<div>
					<h3 class="nowrap"><?php echo self::$txt->Profile->changelogin; ?></h3>
		            <p class="input-large">
						<input type="text" name="login" id="login" placeholder="<?php echo self::$txt->Profile->newlogin; ?>">
		                <label class="fa fa-user" for="login" aria-hidden="true"></label>
		            </p>
		            <input type="submit" class="btn btn-profile" onclick="changeLogin()" value="<?php echo self::$txt->Global->submit; ?>">
		            <div id="changeLoginReturn"></div>
				</div>
				<div>
					<h3 class="nowrap"><?php echo self::$txt->Profile->changemail; ?></h3>
		            <p class="input-large">
		                <input type="text" name="changemail" id="changemail" placeholder="<?php echo self::$txt->Profile->changemail; ?>">
						<label class="fa fa-envelope" for="changemail" aria-hidden="true"></label>
		            </p>
		            <input type="submit" class="btn btn-profile" onclick="changeMail()" value="<?php echo self::$txt->Global->submit; ?>">
		            <div id="changeMailReturn"></div>
				</div>
			</div>
        </fieldset>

        <fieldset>
            <legend><?php echo self::$txt->Profile->pwdpp; ?></legend>
			<div class="bloc-input">
				<div>
					<h3><?php echo self::$txt->Profile->changepwd; ?></h3>
	                <p class="input-large">
						<input type="password" name="old_pwd" id="old_pwd" placeholder="<?php echo self::$txt->Profile->oldpwd; ?>">
	                    <label class="fa fa-lock" for="old_pwd" aria-hidden="true"></label>
	                </p>
					<p class="input-large">
						<input type="password" name="new_pwd" id="new_pwd" placeholder="<?php echo self::$txt->Profile->newpwd; ?>">
	                    <label class="fa fa-lock" for="new_pwd" aria-hidden="true"></label>
	                </p>
					<p class="input-large">
						<input type="password" name="pwd_confirm" id="pwd_confirm" placeholder="<?php echo self::$txt->Register->confirm; ?>">
	                    <label class="fa fa-lock" for="pwd_confirm" aria-hidden="true"></label>
	                </p>
                	<input type="submit" class="btn btn-profile" onclick="changePassword()" value="<?php echo self::$txt->Global->submit; ?>">
                	<div id="changePasswordReturn"></div>
				</div>
				<div>
	                <h3><?php echo self::$txt->Profile->changepp; ?></h3>
	                <p class="input-large">
						<input type="password" name="oldpp" id="oldpp" placeholder="<?php echo self::$txt->Profile->oldpp; ?>">
	                    <label class="fa fa-lock" for="oldpp" aria-hidden="true"></label>
	                </p>
					<p class="input-large">
						<input type="password" name="newpp" id="newpp" placeholder="<?php echo self::$txt->Profile->newpp; ?>">
	                    <label class="fa fa-lock" for="newpp" aria-hidden="true"></label>
	                </p>
					<p class="input-large">
						<input type="password" name="ppconfirm" id="ppconfirm" placeholder="<?php echo self::$txt->Register->confirm; ?>">
	                    <label class="fa fa-lock" for="ppconfirm" aria-hidden="true"></label>
	                </p>
	                <input type="submit" class="btn btn-profile" onclick="changeCek()" value="<?php echo self::$txt->Global->submit; ?>">
	                <div id="changePassPhraseReturn"></div>
				</div>
            </div>
		</fieldset>

        <fieldset>
            <legend><?php echo self::$txt->Profile->otheroptions; ?></legend>
			<h3><?php echo self::$txt->Profile->filedetails; ?></h3>
            <p class="input-large">
                <input type="checkbox" name="details" id="details"  onclick="changeDetails()">
                <label for="details"><?php echo self::$txt->Profile->details; ?></label>
            </p>
            <div id="changeDetailsReturn"></div>
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
			<h3><?php echo self::$txt->Profile->deleteAccount; ?></h3>
			<p class="input-large">
                <input type="checkbox" name="delete" id="delete">
                <label for="delete"><?php echo self::$txt->Profile->iwant; ?></label>
            </p>
			<input type="submit" class="btn btn-warning" onclick="ConfirmDelete()" value="<?php echo self::$txt->Profile->deleteAccount; ?>">
			<div id="deleteUserReturn"></div>
		</fieldset>
    </div>
<?php
    echo $_t->getFooter();
?>
