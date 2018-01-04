<?php
	/* Upgrade view */
    use \library\MVC as l;
	$_t = new l\Template(self::$txt->UserMenu->moreStorage);

	$_t->addCss([
		'blue/blue',
	    'blue/container',
	    'blue/header',
	    'blue/inputs',
	    'blue/menu',
	    'blue/section-large-content'
	]);

	echo $_t->getHead();
	echo $_t->getHeader();
?>
	<div id="container">
        <section id="large-content">
			<?php echo $msg; ?>
			<h1><?php echo self::$txt->Upgrade->offers; ?></h1>
			<div class="bloc">
				<div class="green"><?php echo self::$txt->Upgrade->mue; ?></div>
				<ul>
					<?php echo $offers; ?>
				</ul>
			</div>

			<h2><?php echo self::$txt->Upgrade->history; ?></h2>
			<div class="bloc">
				<table>
					<tr>
						<th><?php echo self::$txt->User->size; ?></th>
						<th><?php echo self::$txt->Upgrade->price; ?></th>
						<th><?php echo self::$txt->Upgrade->start_date; ?></th>
						<th><?php echo self::$txt->Upgrade->end_date; ?></th>
						<th></th>
					</tr>
					<?php echo $history; ?>
				</table>
			</div>
        </section>
	</div>
<?php
   echo $_t->getFooter();
?>
