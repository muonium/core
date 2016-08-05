<?php
    /*
	* @name            : vProfile.php
	* @description     : Profile view (edit profile)
	* @authors         : Dylan Clement <dylanclement7@protonmail.ch>
	*/

    $_t = new Template($this->txt->Global->user);
    $_t->addCss("home_global");
    $_t->addCss("Interface/new_design");
    $_t->addJs("Interface/global");
    $_t->addJs("Interface/Request");
    $_t->addJs("Profile/profile");
    $_t->addJs("Login/sha512");
    $_t->addScript("text/javascript","window.onload = function() {QC.init();}");
    $_t->getHeader();
?>
<body>
        <header>
            <div id="logo"></div>
            <div id="user">
                <p><img src="<?php echo MVC_ROOT; ?>/public/pictures/header/bug.svg" /><br /><?php echo_h($this->txt->UserMenu->bug); ?></p>
                <p><img src="<?php echo MVC_ROOT; ?>/public/pictures/header/help.svg" /><br /><?php echo_h($this->txt->UserMenu->help); ?></p>
                <p><img src="<?php echo MVC_ROOT; ?>/public/pictures/header/settings.svg" /><br /><?php echo_h($this->txt->UserMenu->settings); ?></p>
                <p><a href="<?php echo MVC_ROOT; ?>/Profile"><img src="<?php echo MVC_ROOT; ?>/public/pictures/header/user.svg" /><br /><?php echo_h($this->txt->UserMenu->profile); ?></a></p>
                <p><a href="<?php echo MVC_ROOT; ?>/Logout"><img src="<?php echo MVC_ROOT; ?>/public/pictures/header/user.svg" /><br /><?php echo_h($this->txt->UserMenu->logout); ?></a></p>
            </div>
        </header>
    
        <section id="language">
            <div>
                <?php $this->getLanguageSelector(); ?>
            </div>
        </section>
        
        <section id="desktop">
            <p>
                ID : <?php echo $_SESSION['id']; ?>
                <fieldset>
                    <legend><?php echo_h($this->txt->Profile->changelogin); ?></legend>
                    <p>
                        <input type="text" name="login" id="login" placeholder="<?php echo_h($this->txt->Profile->newlogin); ?>">
                    </p>
                    <input type="submit" onclick="changeLogin()">
                    <div id="changeLoginReturn"></div>
                </fieldset>
                <br />
            </p>
            
            <p>
                <fieldset>
                    <legend><?php echo_h($this->txt->Profile->changepwd); ?></legend>
                    
                    <p>
                        <input type="password" name="oldpwd" id="oldpwd" placeholder="<?php echo_h($this->txt->Profile->oldpwd); ?>">
                    </p>
                    <p>
                        <input type="password" name="newpwd" id="newpwd" placeholder="<?php echo_h($this->txt->Profile->newpwd); ?>">
                    </p>
                    <p>
                        <input type="password" name="pwdconfirm" id="pwdconfirm" placeholder="<?php echo_h($this->txt->Register->confirm); ?>">
                    </p>
                    <input type="submit" onclick="changePassword()">
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
                    <input type="submit" onclick="changePassPhrase()">
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
                    <input type="submit" onclick="changeAuth()">
                    <div id="changeAuthReturn"></div>
                </fieldset>
            </p>
        </section>
</body>
<?php
    $_t->getFooter();
?>