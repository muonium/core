<?php
	/* Register page */
    use \library\MVC as l;
	$_t = new l\Template(self::$txt->Global->register);
	$_t->addCss([
		'blue/blue',
	    'blue/container',
	    'blue/header',
	    'blue/inputs',
	    'blue/menu',
	    'blue/section-large-content'
	])->addJS([
		'src/crypto/sjcl',
    	'base64',
    	'sha512',
    	'mui_hash',
    	'Register/log_register'
	]);

	echo $_t->getHead();
	echo $_t->getHeader();
?>
	<div id="container">
        <section id="large-content">
            <h1><?php echo self::$txt->Global->register; ?></h1>

            <div id="form">
                <p>
					<label class="fa fa-envelope" for="field_mail" aria-hidden="true"></label><!--
                    --><input type="text" id="field_mail" placeholder="<?php echo self::$txt->Register->email; ?>..." autofocus>
				</p>

				<p>
					<label class="fa fa-user" for="field_login" aria-hidden="true"></label><!--
                    --><input type="text" id="field_login" placeholder="<?php echo self::$txt->Register->login; ?>..." />
				</p>

				<p>
					<label class="fa fa-key" for="field_pass" aria-hidden="true"></label><!--
                    --><input type="password" id="field_pass" placeholder="<?php echo self::$txt->Register->password; ?>..." />
				</p>

				<p>
					<label class="fa fa-key" for="field_pass_confirm" aria-hidden="true"></label><!--
                    --><input type="password" id="field_pass_confirm" placeholder="<?php echo self::$txt->Register->confirm; ?>..." />
				</p>

				<p>
					<label class="fa fa-key" for="field_passphrase" aria-hidden="true"></label><!--
                    --><input type="password" id="field_passphrase" placeholder="<?php echo self::$txt->Register->passphrase; ?>..."/>
				</p>

				<p>
					<label class="fa fa-key" for="field_passphrase_confirm" aria-hidden="true"></label><!--
                    --><input type="password" id="field_passphrase_confirm" placeholder="<?php echo self::$txt->Register->confirm; ?>..."/>
				</p>

                <p>
					<input type="checkbox" id="doubleAuth" name="doubleAuth"> <label for="doubleAuth"><?php echo self::$txt->Register->doubleAuth; ?></label>
                	&nbsp;&nbsp;<a href="<?php echo MVC_ROOT; ?>/Login"><?php echo self::$txt->Register->alreadyregistered; ?></a>
				</p>

                <input type="submit" value="<?php echo self::$txt->Global->register; ?>" onclick="sendRegisterRequest(event)">
            </div>

            <div id="return">
                <p class="error"><?php //echo self::$txt->Register->impossible; ?></p>
            </div>
        </section>
	</div>
<?php
   echo $_t->getFooter();
?>
