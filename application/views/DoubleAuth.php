<?php
/*
	* @name            : DoubleAuth.php
	* @description     : Double auth view
	* @authors         : Dylan Clement <dylanclement7@protonmail.ch>
*/
    use \library\MVC as l;
    $_t = new l\Template($this->txt->Global->login);
    $_t->addCss("blue/blue");
    $_t->addCss("blue/container");
    $_t->addCss("blue/header");
    $_t->addCss("blue/inputs");
    $_t->addCss("blue/menu");
    $_t->addCss("blue/section-large-content");
	$_t->addJs("check");
    $_t->getHeader();
?>
<body class="grey">
    <header>
        <div id="logo">
            <a href="https://muonium.io" target="_blank">
                <img src="public/pictures/logos/muonium_H_06.png">
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
            <h2><?php echo_h($this->txt->Global->login); ?></h2>

            <div id="form">
                <form method="post" action="<?php echo MVC_ROOT; ?>/Login/AuthCode">
                    <p style="color:red"><?php if(!empty($this->_message)) { echo $this->_message; } ?></p>
                    <input type="text" name="code" placeholder="<?php echo_h($this->txt->Login->codeMail); ?>" required>
                    <input type="submit">
                </form>
            </div>
        </section>
    </div>
</body>
<?php
$_t->getFooter();
?>
