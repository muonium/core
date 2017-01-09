<?php
/*
* @name            : vTransfer.php
* @description     : Transfer view
* @authors         : Romain Claveau <romain.claveau@protonmail.ch>, Dylan Clement <dylanclement7@protonmail.ch>
*/
use \library\MVC as l;
$_t = new l\Template($this->txt->Global->transfer);
$_t->addCss("home_global");
$_t->addCss("Interface/new_design");
$_t->addCss("Interface/box");

// JS Modules
$_t->addJs("Interface/modules/Arrows");
$_t->addJs("Interface/modules/Box");
$_t->addJs("Interface/modules/Decryption");
$_t->addJs("Interface/modules/Encryption");
$_t->addJs("Interface/modules/ExtIcons");
$_t->addJs("Interface/modules/Favorites");
$_t->addJs("Interface/modules/Files");
$_t->addJs("Interface/modules/Folders");
$_t->addJs("Interface/modules/Move");
$_t->addJs("Interface/modules/Rm");
$_t->addJs("Interface/modules/Selection");
$_t->addJs("Interface/modules/Time");
$_t->addJs("Interface/modules/Trash");
$_t->addJs("Interface/modules/Upload");

$_t->addJs("check");
$_t->addJs("src/crypto/sjcl");
$_t->addJs("Interface/idb.filesystem.min");
$_t->addJs("Interface/Request");
$_t->addJs("Interface/interface");
$_t->getHeader();
?>
<body>
	<header>
		<div id="logo"></div>
		<?php $_t->getRegisteredMenu($this->txt->UserMenu); ?>
	</header>

	<section id="language">
		<div>
			<?php $this->getLanguageSelector(); ?>
		</div>
	</section>

	<section id="toolbar">
		<div onclick="Request.load('Recent', 'DefaultAction')" id="toolbar_button_recents">
			<img src="<?php echo MVC_ROOT; ?>/public/pictures/toolbar/recent.svg" /><br /><?php echo_h($this->txt->Toolbar->recents); ?>
		</div>
		<div onclick="Request.load('Favorites', 'DefaultAction')" id="toolbar_button_favorite">
			<img src="<?php echo MVC_ROOT; ?>/public/pictures/toolbar/favorite.svg" /><br /><?php echo_h($this->txt->Toolbar->favorites); ?>
		</div>
		<div onclick="Request.load('User', 'DefaultAction')" id="toolbar_button_general">
			<img src="<?php echo MVC_ROOT; ?>/public/pictures/toolbar/folder.svg" /><br /><?php echo_h($this->txt->Toolbar->general); ?>
		</div>
		<div onclick="Request.load('Sharing', 'DefaultAction')" id="toolbar_button_share">
			<img src="<?php echo MVC_ROOT; ?>/public/pictures/toolbar/share.svg" /><br /><?php echo_h($this->txt->Toolbar->shared); ?>
		</div>
		<div class="selected" onclick="Request.load('Transfer', 'DefaultAction')" id="toolbar_button_transfers">
			<img src="<?php echo MVC_ROOT; ?>/public/pictures/toolbar/transfer.svg" /><br /><?php echo_h($this->txt->Toolbar->transfers); ?>
		</div>
	</section>

	<section id="desktop">
		<img src="<?php echo MVC_ROOT; ?>/public/pictures/desktop/arrow.svg" class="arrow transfers" />
	</section>
</body>
<?php
$_t->getFooter();
?>
