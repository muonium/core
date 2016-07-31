<?php
    /*
	* @name            : vBug.php
	* @description     : Notify a bug view
	* @authors         : Dylan Clement <dylanclement7@protonmail.ch>
	*/

    $_t = new Template($this->txt->Global->user);
    $_t->addCss("home_global");
    $_t->addCss("Interface/new_design");
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
            <p></p>
            
            <form method="post" action="">
            </form>
        </section>
</body>
<?php
    $_t->getFooter();
?>