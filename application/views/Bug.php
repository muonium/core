<?php
    /* Notify a bug page */
    use \library\MVC as l;
    $_t = new l\Template(self::$txt->Global->bug);

    $_t->addCss([
		'blue/blue',
    	'blue/container',
    	'blue/header',
    	'blue/inputs',
    	'blue/menu',
    	'blue/section-large-content'
	])->addJs(['Bug', 'check']);

	echo $_t->getHead();
	echo $_t->getHeader();
?>
    <div id="container">
        <section id="large-content">
            <h1><?php echo self::$txt->Global->bug; ?></h1>

            <p><strong><?php if(!empty($this->_message)) { echo_h($this->_message); } ?></strong></p>

            <form method="post" action="<?php echo MVC_ROOT; ?>/Bug/Form" class="block">
                <p>
                    <label for="os"><?php echo self::$txt->Bug->os; ?>* :</label>
                    <select name="os" id="os" required>
                        <option value="">-------</option>
                        <?php $this->printValues('os'); ?>
                    </select>
                </p>

                <p>
                    <label for="browser"><?php echo self::$txt->Bug->browser; ?>* :</label>
                    <select name="browser" id="browser" required>
                        <option value="">-------</option>
                        <?php $this->printValues('browser'); ?>
                    </select>
                </p>

                <p>
                    <label for="browserVersion"><?php echo self::$txt->Bug->browserVersion; ?> :</label>
                    <input type="text" name="browserVersion" id="browserVersion" value="<?php if(!empty($_POST['browserVersion'])) { echo_h($_POST['browserVersion']); } ?>">
                </p>

                <p>
                    <label for="message"><?php echo self::$txt->Bug->message; ?>* :</label>
                    <textarea name="message" id="message" cols="50" rows="5"><?php if(!empty($_POST['message'])) { echo_h($_POST['message']); } ?></textarea>
                </p>

				<input type="submit" value="OK">
            </form>
        </section>
    </div>
<?php
    echo $_t->getFooter();
?>
