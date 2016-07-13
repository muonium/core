<?php
	/*
	* @name            : vLostPassForm.php
	* @description     : Lost pass form view (password or passphrase)
	* @authors         : Dylan Clement <dylanclement7@protonmail.ch>
	*/

	$_t = new Template($this->txt->Global->validate);
    $_t->addCss("home_global");
    $_t->addCss("Register/home_register");
    $_t->addJs("LostPass/lostpass");
    $_t->getHeader();
?>
<body>
        <section id="language">
            <div>
                <?php $this->getLanguageSelector(); ?>
            </div>
        </section>

        <section id="header">
            <div id="logo"><img src="<?php echo MVC_ROOT; ?>/public/pictures/register/logo_anime.svg" /></div>
        </section>

        <section id="content">
            <div id="back"><p><a href="../photon/"><?php echo $this->txt->Global->back; ?></a></p></div>

            <div id="avatar"><p><img src="<?php echo MVC_ROOT; ?>/public/pictures/register/user.svg" /></p></div>
            <div id="text"><p><?php echo $this->txt->Login->forgot; ?></p></div>

            <div>
                <?php echo $this->err_msg; ?><br />
                <div id="returnArea"></div>
                <fieldset>
                    <legend><?php echo $this->txt->Profile->changepwd; ?></legend>
                    
                    <p><label for="pwd"><?php echo $this->txt->Profile->newpwd; ?></label>
                    <input type="text" name="pwd" id="pwd"></p>
                
                    <p><label for="pwd_confirm"><?php echo $this->txt->Register->confirm; ?></label>
                    <input type="text" name="pwd_confirm" id="pwd_confirm"></p>
                </fieldset>
                
                <fieldset>
                    <legend><?php echo $this->txt->Profile->changepp; ?></legend>
                    
                    <p><label for="pp"><?php echo $this->txt->Profile->newpp; ?></label>
                    <input type="text" name="pp" id="pp"></p>
                    
                    <p><label for="pp_confirm"><?php echo $this->txt->Register->confirm; ?></label>
                    <input type="text" name="pp_confirm" id="pp_confirm"></p>
                </fieldset>
                
                <input type="button" onclick="changePass()">
            </div>
        </section>
</body>
<?php
   $_t->getFooter();
?>
