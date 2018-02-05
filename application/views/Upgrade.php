<?php
	/* Upgrade view */
    use \library\MVC as l;
	$_t = new l\Template(self::$txt->UserMenu->moreStorage);

	$_t->addCss([
		'2018/style'
	]);

	echo $_t->getHead();
	echo $_t->getHeader();
	echo $_t->getSidebar();
?>
	<div class="container-large">
		<h1><?php echo self::$txt->Upgrade->upgradeMui; ?></h1>

		<?php if(isset($msg)) { echo $msg; } ?>
		<p class="em"><?php echo self::$txt->Upgrade->mue; ?></p>

		<div class="bloc-offers">
			<?php echo $offers; ?>
		</div>

		<h2 class="em"><?php echo self::$txt->Upgrade->history; ?></h2>
		<table class="table-large table-responsive">
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
<?php
   echo $_t->getFooter();
?>
