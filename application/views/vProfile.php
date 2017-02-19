<?php
    /*
	* @name            : vProfile.php
	* @description     : Profile view (edit profile)
	* @authors         : Dylan Clement <dylanclement7@protonmail.ch>
	*/
    use \library\MVC as l;
    $_t = new l\Template($this->txt->Global->profile);
    $_t->addCss("home_global");
    $_t->addCss("Interface/new_design");
	$_t->addJs("check");
    $_t->addJs("Interface/interface");
    $_t->addJs("Interface/Request");
    $_t->addJs("Profile/profile");
    $_t->addJs("Login/sha512");
	$_t->addJs("mui_hash");
	$_t->addJs("src/crypto/sjcl");
	$_t->addJs("base64");
    $_t->getHeader();
?>
<body>
        <header>
            <div id="logo"></div>
            <?php $_t->getRegisteredMenu($this->txt->UserMenu); ?>
        </header>

        <section id="language">
            <div>
                <?php $this->getLanguageSelector(); ?>
            </div>
        </section>

        <section id="desktop">
			<p><a href="<?php echo MVC_ROOT; ?>/User"><< <?php echo_h($this->txt->Global->back); ?></a></p><br />
            <p>
                ID : <?php echo $_SESSION['id']; ?>
                <fieldset>
                    <legend><?php echo_h($this->txt->Profile->changelogin); ?></legend>
                    <p>
                        <input type="text" name="login" id="login" placeholder="<?php echo_h($this->txt->Profile->newlogin); ?>">
                    </p>
                    <input type="submit" onclick="changeLogin()" value="OK">
                    <div id="changeLoginReturn"></div>
                </fieldset>
                <br />
            </p>

		<!-- Add change email button -->
            <p>
                <fieldset>
                    <legend><?php echo_h($this->txt->Profile->changemail); ?></legend>
                    <p>
                        <input type="text" name="changemail" id="changemail" placeholder="<?php echo_h($this->txt->Profile->changemail); ?>">
                    </p>
                    <input type="submit" onclick="changeMail()" value="OK">
                    <div id="changeMailReturn"></div>
                </fieldset>
                <br />
            </p>
            <!--                        -->
			<!-- Add delete button user  -->
            <p>
				<fieldset>
					<legend><?php echo_h($this->txt->Profile->deleteAccount); ?></legend>
					<input type="submit" onclick="ConfirmDelete()" value="OK">
					<div id="deleteUserReturn"></div>
				</fieldset>
				<br />
			</p>
			<!--    -->

            <p>
                <fieldset>
                    <legend><?php echo_h($this->txt->Profile->changepwd); ?></legend>

                    <p>
                        <input type="password" name="old_pwd" id="old_pwd" placeholder="<?php echo_h($this->txt->Profile->oldpwd); ?>">
                    </p>
                    <p>
                        <input type="password" name="new_pwd" id="new_pwd" placeholder="<?php echo_h($this->txt->Profile->newpwd); ?>">
                    </p>
                    <p>
                        <input type="password" name="pwd_confirm" id="pwd_confirm" placeholder="<?php echo_h($this->txt->Register->confirm); ?>">
                    </p>
                    <input type="submit" onclick="changePassword()" value="OK">
                    <div id="changePasswordReturn"></div>
                </fieldset>
                <br />
            </p>

            <p>
                <fieldset>
                    <legend><?php echo_h($this->txt->Profile->changepp); ?></legend>
                    <p>
                        <input type="password" name="oldpp" id="oldpp" placeholder="<?php echo_h($this->txt->Profile->oldpp); ?>">
                    </p>
                    <p>
                        <input type="password" name="newpp" id="newpp" placeholder="<?php echo_h($this->txt->Profile->newpp); ?>">
                    </p>
                    <p>
                        <input type="password" name="ppconfirm" id="ppconfirm" placeholder="<?php echo_h($this->txt->Register->confirm); ?>">
                    </p>
                    <input type="submit" onclick="changeCek()" value="OK">
                    <div id="changePassPhraseReturn"></div>
                </fieldset>
                <br />
            </p>

            <p>
                <fieldset>
                    <legend><?php echo_h($this->txt->Profile->doubleAuth); ?></legend>

                    <p>
                        <?php echo_h($this->txt->Register->doubleAuth); ?>
                        <input type="checkbox" name="doubleAuth" id="doubleAuth"<?php if($this->_modelUser->getDoubleAuth()) { echo ' checked'; } ?>>
                    </p>
                    <input type="submit" onclick="changeAuth()" value="OK">
                    <div id="changeAuthReturn"></div>
                </fieldset>
            </p>
        </section>
</body>
<?php
    $_t->getFooter();
?>
