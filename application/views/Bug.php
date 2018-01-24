<?php
    /* Notify a bug page */
    use \library\MVC as l;
    $_t = new l\Template(self::$txt->Global->bug);

    $_t->addCss([
		'2018/style'
	])->addJs(['Bug', 'check']);

	echo $_t->getHead();
	echo $_t->getHeader();
	echo $_t->getSidebar();
?>
    <div class="container-large">
		<form method="post" action="<?php echo MVC_ROOT; ?>/Bug/Form" class="form-bug">
            <h1><?php echo self::$txt->Global->bug; ?></h1>

            <p><strong><?php if(!empty($this->_message)) { echo_h($this->_message); } ?></strong></p>

            <fieldset>
                <legend><?php echo self::$txt->Bug->os; ?>*</legend>
				<p class="input-large">
                    <select name="os" id="os" required>
                        <option value="">-------</option>
                        <?php $this->printValues('os'); ?>
                    </select>
				</p>
            </fieldset>

			<fieldset>
				<legend><?php echo self::$txt->Bug->browser; ?>*</legend>
                <p class="input-large">
                    <select name="browser" id="browser" required>
                        <option value="">-------</option>
                        <?php $this->printValues('browser'); ?>
                    </select>
                </p>
			</fieldset>

            <p class="input-large">
                <input type="text" name="browserVersion" id="browserVersion" value="<?php if(!empty($_POST['browserVersion'])) { echo_h($_POST['browserVersion']); } ?>" placeholder="<?php echo self::$txt->Bug->browserVersion; ?>">
            </p>

			<fieldset>
				<legend><?php echo self::$txt->Bug->message; ?>*</legend>
                <p class="input-large">
                    <textarea name="message" id="message" cols="50" rows="5" required><?php if(!empty($_POST['message'])) { echo_h($_POST['message']); } ?></textarea>
                </p>
			</fieldset>

			<input type="submit" value="<?php echo self::$txt->Bug->send; ?>" class="btn btn-required" disabled>
        </form>
    </div>
<?php
    echo $_t->getFooter();
?>
