<?php
	/* Lost pass form */
    use \library\MVC as l;
	$_t = new l\Template(self::$txt->Login->forgot);

	$_t->addCss([
		'blue/blue',
	    'blue/container',
	    'blue/header',
	    'blue/inputs',
	    'blue/menu',
	    'blue/section-large-content'
	])->addJs([
		'base64',
	    'sha512',
	    'mui_hash',
	    'LostPass/lostpass'
	]);

	echo $_t->getHead();
	echo $_t->getHeader();
?>
	<div id="container">
        <section id="large-content">
            <h1><?php echo self::$txt->Login->forgot; ?></h1>

			<div>
                <?php echo_h($this->err_msg); ?><br>
                <div id="returnArea"></div>
                <fieldset>
                    <legend><?php echo self::$txt->Profile->changepwd; ?></legend>

                    <p><label for="pwd"><?php echo self::$txt->Profile->newpwd; ?></label>
                    <input type="password" name="pwd" id="pwd" autofocus></p>

                    <p><label for="pwd_confirm"><?php echo self::$txt->Register->confirm; ?></label>
                    <input type="password" name="pwd_confirm" id="pwd_confirm"></p>
                </fieldset>

                <?php
				/*
				<fieldset>
                    <legend><?php echo self::$txt->Profile->changepp; ?></legend>
                    <p>
                        <?php echo str_replace("[count]", $this->ppCounter, self::$txt->Profile->warningpp; ?>
                         <?php if($this->ppCounter >= 2) { echo '<br><strong>'.self::$txt->LostPass->reset.'</strong>'; } ?>
                    </p>
                    <p><label for="pp"><?php echo self::$txt->Profile->newpp; ?></label>
                    <input type="password" name="pp" id="pp"></p>

                    <p><label for="pp_confirm"><?php echo self::$txt->Register->confirm; ?></label>
                    <input type="password" name="pp_confirm" id="pp_confirm"></p>
                </fieldset>
				*/
				?>

                <input type="button" onclick="changePass()" value="<?php echo self::$txt->Global->submit; ?>">
            </div>
        </section>
	</div>
<?php
   echo $_t->getFooter();
?>
