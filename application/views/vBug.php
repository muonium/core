<?php
    /*
	* @name            : vBug.php
	* @description     : Notify a bug view
	* @authors         : Dylan Clement <dylanclement7@protonmail.ch>
	*/
    use \library\MVC as l;
    $_t = new l\Template($this->txt->Global->bug);
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
            <h2><?php echo_h($this->txt->Global->bug); ?></h2>

            <p><strong><?php if(!empty($this->_message)) { echo_h($this->_message); } ?></strong></p>

            <form method="post" action="<?php echo MVC_ROOT; ?>/Bug/Form" class="block">
                <p>
                    <label for="os"><?php echo_h($this->txt->Bug->os); ?>* :</label>
                    <select name="os" id="os" required>
                        <option value="">-------</option>
                        <?php $this->printValues('os'); ?>
                    </select>
                </p>

                <p>
                    <label for="browser"><?php echo_h($this->txt->Bug->browser); ?>* :</label>
                    <select name="browser" id="browser" required>
                        <option value="">-------</option>
                        <?php $this->printValues('browser'); ?>
                    </select>
                </p>

                <p>
                    <label for="browserVersion"><?php echo_h($this->txt->Bug->browserVersion); ?> :</label>
                    <input type="text" name="browserVersion" id="browserVersion" value="<?php if(!empty($_POST['browserVersion'])) { echo_h($_POST['browserVersion']); } ?>">
                </p>

                <p>
                    <label for="message"><?php echo_h($this->txt->Bug->message); ?>* :</label>
                    <textarea name="message" id="message" cols="50" rows="5"><?php if(!empty($_POST['message'])) { echo_h($_POST['message']); } ?></textarea>
                </p>

				<input type="submit" value="OK">
            </form>
        </section>
    </div>
</body>
<?php
    $_t->getFooter();
?>
