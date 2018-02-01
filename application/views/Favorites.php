<?php
	/* Favorites view */
    use \library\MVC as l;
    $_t = new l\Template(self::$txt->Global->favorites);

	$_t->addCss([
		'Interface/box',
	    'Interface/MessageBox',
	    'Interface/progress_bar',
		'2018/style',
		'2018/transfers',
		'2018/tree',
		'2018/selection'
	])->addJs([
		'Interface/modules/Arrows',
		'Interface/modules/Box',
		'Interface/modules/Decryption',
		'Interface/modules/Encryption',
		'Interface/modules/ExtIcons',
		'Interface/modules/Favorites',
		'Interface/modules/Files',
		'Interface/modules/Folders',
	    'Interface/modules/MessageBox',
		'Interface/modules/Move',
		'Interface/modules/Rm',
		'Interface/modules/Selection',
		'Interface/modules/Time',
		'Interface/modules/Transfers',
		'Interface/modules/Trash',
		'Interface/modules/Upload',
		'check',
		'src/crypto/sjcl',
		'Interface/idb.filesystem.min',
	    'Interface/Request',
		'Interface/interface'
	]);

	echo $_t->getHead();
	echo $_t->getHeader();
	echo $_t->getSidebar();
?>
	<div class="container-max">
		<div id="display">
			<input type="radio" id="display_list" name="display">
			<label for="display_list" class="nomargin"><i class="fa fa-th-list" aria-hidden="true"></i></label>

			<input type="radio" id="display_mosaic" name="display" checked>
			<label for="display_mosaic" class="nomargin"><i class="fa fa-th-large" aria-hidden="true"></i></label>
		</div>

		<section id="desktop">
			<div id="mui">
				<?php echo $favorites; ?>
            </div>
        </section>
	</div>
<?php
    echo $_t->getFooter();
?>
