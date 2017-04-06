<?php
	/*
	* @name            : vLostPass.php
	* @description     : Lost pass view (password or passphrase)
	* @authors         : Dylan Clement <dylanclement7@protonmail.ch>
	*/
    use \library\MVC as l;
	$_t = new l\Template($this->txt->Login->forgot);
	$_t->addCss("fonts/roboto");
	$_t->addCss("blue/blue");
    $_t->addCss("blue/container");
    $_t->addCss("blue/header");
    $_t->addCss("blue/inputs");
    $_t->addCss("blue/menu");
    $_t->addCss("blue/section-large-content");
   	$_t->getHeader();
?>
<body class="grey">
	<header>
        <div id="logo"><img src="public/pictures/logos/muonium_H_06.png"></div>
        <ul>
            <li><a href="https://muonium.ch/photon/"><?php echo $this->txt->Global->back; ?></a></li>
        </ul>
        <section id="language">
            <div>
                <?php $this->getLanguageSelector(); ?>
            </div>
        </section>
    </header>

	<div id="container">
        <section id="large-content">
            <h2><?php echo_h($this->txt->Login->forgot); ?></h2>

            <br /><br />
			<form method="post" action="<?php echo MVC_ROOT; ?>/LostPass/SendMail">
                <?php echo $this->err_msg; ?><br />
                <label for="user"><?php echo_h($this->txt->LostPass->user); ?> :</label>
                <input type="text" name="user" id="user" required="required" value="<?php if(!empty($_POST['user'])) { echo_h($_POST['user']); } ?>">
                <input type="submit">
            </form>
        </section>
	</div>
</body>
<?php
   $_t->getFooter();
?>
