<?php
    /*
	* @name            : Profile.php
	* @description     : Profile view (edit profile)
	* @authors         : Dylan Clement <dylanclement7@protonmail.ch>
	*/
    use \library\MVC as l;
    $_t = new l\Template($this->txt->Global->profile);
    $_t->addCss("blue/blue");
    $_t->addCss("blue/container");
    $_t->addCss("blue/header");
    $_t->addCss("blue/inputs");
    $_t->addCss("blue/menu");
    $_t->addCss("blue/section-large-content");

	$_t->addJs("check");
    $_t->addJs("Interface/interface");
    $_t->addJs("Interface/Request");
    $_t->addJs("Profile/profile");
    $_t->addJs("sha512");
	$_t->addJs("mui_hash");
	$_t->addJs("src/crypto/sjcl");
	$_t->addJs("base64");
    $_t->getHeader();
?>
<body class="grey">
    <header>
        <div id="logo">
            <a href="https://muonium.io" target="_blank">
                <img src="public/pictures/logos/muonium_H_06.png" title="<?php echo $this->txt->Global->home; ?>" alt="<?php echo $this->txt->Global->home; ?>">
            </a>
        </div>
        <ul>
            <li><a href="User"><?php echo $this->txt->Global->back; ?></a></li>
        </ul>
        <section id="language">
            <div>
                <?php $this->getLanguageSelector(); ?>
            </div>
        </section>
    </header>

    <div id="container">
        <section id="large-content">
			<fieldset>
				<legend><?php echo_h($this->txt->Global->profile); ?></legend>
	            <ul class="list">
					<li><?php echo $this->txt->Register->login.'&nbsp;: '.htmlentities($_SESSION['login']); ?></li>
					<li><?php echo $this->txt->Register->email.'&nbsp;: '.$this->_modelUser->getEmail(); ?></li>
					<li>ID&nbsp;: <?php echo $_SESSION['id']; ?></li>
				</ul>
			</fieldset>
			<p>
                <fieldset>
                    <legend><?php echo_h($this->txt->Profile->changelogin); ?></legend>
                    <p>
                        <label class="fa fa-user" for="login" aria-hidden="true"></label><!--
                        --><input type="text" name="login" id="login" placeholder="<?php echo_h($this->txt->Profile->newlogin); ?>">
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
                        <label class="fa fa-envelope" for="changemail" aria-hidden="true"></label><!--
                        --><input type="text" name="changemail" id="changemail" placeholder="<?php echo_h($this->txt->Profile->changemail); ?>">
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
                        <label class="fa fa-key" for="old_pwd" aria-hidden="true"></label><!--
                        --><input type="password" name="old_pwd" id="old_pwd" placeholder="<?php echo_h($this->txt->Profile->oldpwd); ?>">
                    </p>
                    <p>
                        <label class="fa fa-key" for="new_pwd" aria-hidden="true"></label><!--
                        --><input type="password" name="new_pwd" id="new_pwd" placeholder="<?php echo_h($this->txt->Profile->newpwd); ?>">
                    </p>
                    <p>
                        <label class="fa fa-key" for="pwd_confirm" aria-hidden="true"></label><!--
                        --><input type="password" name="pwd_confirm" id="pwd_confirm" placeholder="<?php echo_h($this->txt->Register->confirm); ?>">
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
                        <label class="fa fa-key" for="oldpp" aria-hidden="true"></label><!--
                        --><input type="password" name="oldpp" id="oldpp" placeholder="<?php echo_h($this->txt->Profile->oldpp); ?>">
                    </p>
                    <p>
                        <label class="fa fa-key" for="newpp" aria-hidden="true"></label><!--
                        --><input type="password" name="newpp" id="newpp" placeholder="<?php echo_h($this->txt->Profile->newpp); ?>">
                    </p>
                    <p>
                        <label class="fa fa-key" for="ppconfirm" aria-hidden="true"></label><!--
                        --><input type="password" name="ppconfirm" id="ppconfirm" placeholder="<?php echo_h($this->txt->Register->confirm); ?>">
                    </p>
                    <input type="submit" onclick="changeCek()" value="OK">
                    <div id="changePassPhraseReturn"></div>
                </fieldset>
                <br />
            </p>

            <p>
                <fieldset>
                    <legend>Details</legend>

                    <p>
                        <input type="checkbox" name="details" id="details">
                        <label for="details"><?php echo_h($this->txt->Profile->details); ?></label>
                    </p>
                    <input type="submit" onclick="changeDetails()" value="OK">
                    <div id="changeDetailsReturn"></div>
                </fieldset>
                <br />
            </p>

            <p>
                <fieldset>
                    <legend><?php echo_h($this->txt->Profile->doubleAuth); ?></legend>

                    <p>
                        <input type="checkbox" name="doubleAuth" id="doubleAuth"<?php if($this->_modelUser->getDoubleAuth()) { echo ' checked'; } ?>>
                        <label for="doubleAuth"><?php echo_h($this->txt->Register->doubleAuth); ?></label>
                    </p>
                    <input type="submit" onclick="changeAuth()" value="OK">
                    <div id="changeAuthReturn"></div>
                </fieldset>
            </p>
        </section>
    </div>
</body>
<?php
    $_t->getFooter();
?>
