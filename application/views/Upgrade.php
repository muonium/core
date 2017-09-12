<?php
	/*
	* @name            : Upgrade.php
	* @description     : Upgrade view
	* @authors         : Dylan Clement <dylanclement7@protonmail.ch>
	*/
    use \library\MVC as l;
	$_t = new l\Template($this->txt->UserMenu->moreStorage);
	$_t->addCss("blue/blue");
    $_t->addCss("blue/container");
    $_t->addCss("blue/header");
    $_t->addCss("blue/inputs");
    $_t->addCss("blue/menu");
    $_t->addCss("blue/section-large-content");
   	$_t->getHeader();
?>
<body class="grey">
	<header>
		<div id="logo">
            <a href="https://muonium.io" target="_blank">
                <img src="public/pictures/logos/muonium_H_06.png" title="<?php echo $this->txt->Global->home; ?>" alt="<?php echo $this->txt->Global->home; ?>">
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
			<h1><?php echo_h($this->txt->Upgrade->offers); ?></h1>
			<div class="bloc">
				<ul>
					<?php echo $offers; ?>
				</ul>
			</div>

			<h2><?php echo_h($this->txt->Upgrade->history); ?></h2>
			<div class="bloc">
				<table>
					<tr>
						<th><?php echo_h($this->txt->User->size); ?></th>
						<th><?php echo_h($this->txt->Upgrade->price); ?></th>
						<th><?php echo_h($this->txt->Upgrade->start_date); ?></th>
						<th><?php echo_h($this->txt->Upgrade->end_date); ?></th>
						<th></th>
					</tr>
					<?php echo $history; ?>
				</table>
			</div>
        </section>
	</div>
</body>
<?php
   $_t->getFooter();
?>
