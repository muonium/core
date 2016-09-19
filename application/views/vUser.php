<?php
    /*
	* @name            : vUser.php
	* @description     : User view (files management)
	* @authors         : Romain Claveau <romain.claveau@protonmail.ch>, Dylan Clement <dylanclement7@protonmail.ch>
	*/
    use \library\MVC as l;
    $_t = new l\Template($this->txt->Global->profile);
    $_t->addCss("home_global");
    $_t->addCss("Interface/new_design");
    $_t->addCss("Interface/box");
    $_t->addJs("Interface/global");
    $_t->addJs("Interface/Request");
    //$_t->addScript("text/javascript","window.onload = function() {QC.init();}");
    $_t->getHeader();
?>
<body>
        <header>
            <div id="logo"></div>
            <div id="user">
                <p><img src="<?php echo MVC_ROOT; ?>/public/pictures/header/bug.svg" /><br /><?php echo_h($this->txt->UserMenu->bug); ?></p>
                <p><img src="<?php echo MVC_ROOT; ?>/public/pictures/header/help.svg" /><br /><?php echo_h($this->txt->UserMenu->help); ?></p>
                <p><img src="<?php echo MVC_ROOT; ?>/public/pictures/header/settings.svg" /><br /><?php echo_h($this->txt->UserMenu->settings); ?></p>
                <p><a href="<?php echo MVC_ROOT; ?>/Profile"><img src="<?php echo MVC_ROOT; ?>/public/pictures/header/user.svg" /><br /><?php echo_h($this->txt->UserMenu->profile); ?></a></p>
                <p><a href="<?php echo MVC_ROOT; ?>/Logout"><img src="<?php echo MVC_ROOT; ?>/public/pictures/header/user.svg" /><br /><?php echo_h($this->txt->UserMenu->logout); ?></a></p>
            </div>
        </header>
    
        <section id="language">
            <div>
                <?php $this->getLanguageSelector(); ?>
            </div>
        </section>
        
        <section id="toolbar">
            <div onclick="Muonium.clickEvent(this,'DefaultAction','Recent')" id="toolbar_button_recents">
                <img src="<?php echo MVC_ROOT; ?>/public/pictures/toolbar/recent.svg" /><br /><?php echo_h($this->txt->Toolbar->recents); ?>
            </div>
            <div class="selected" onclick="Muonium.clickEvent(this,'DefaultAction','Favorites')" id="toolbar_button_favorite">
                <img src="<?php echo MVC_ROOT; ?>/public/pictures/toolbar/favorite.svg" /><br /><?php echo_h($this->txt->Toolbar->favorites); ?>
            </div>
            <div onclick="Muonium.clickEvent(this,'DefaultAction','Home')" id="toolbar_button_general">
                <img src="<?php echo MVC_ROOT; ?>/public/pictures/toolbar/folder.svg" /><br /><?php echo_h($this->txt->Toolbar->general); ?>
            </div>
            <div onclick="Muonium.clickEvent(this,'DefaultAction','Sharing')" id="toolbar_button_share">
                <img src="<?php echo MVC_ROOT; ?>/public/pictures/toolbar/share.svg" /><br /><?php echo_h($this->txt->Toolbar->shared); ?>
            </div>
            <div onclick="Muonium.clickEvent(this,'DefaultAction','Transfer')" id="toolbar_button_transfers">
                <img src="<?php echo MVC_ROOT; ?>/public/pictures/toolbar/transfer.svg" /><br /><?php echo_h($this->txt->Toolbar->transfers); ?>
            </div>
        </section>
    
        <section id="desktop">
            <!-- Hidden upload form -->
            <form style="display:none">
                <input type="file" id="upFilesInput" name="files[]" multiple="multiple" style="display:none" onchange="upFiles(this.files);" />
            </form>
            <!-- End -->
            
            <div id="returnArea"></div>
            <div id="progress"></div>
            <div id="tree">
                <?php $this->getTree(); ?>
            </div>
            <img src="<?php echo MVC_ROOT; ?>/public/pictures/desktop/arrow.svg" class="arrow general" />
            
            <div id="desktop_general" class="content">
                <div id="nav">
                    <span class="content">
                        <span class="dir">Home</span>
                        <span class="separator">&gt;</span>
                    </span>
                </div>
                <div id="leftPanel">
                    <div id="listTypes">
                        <p><img src="<?php echo MVC_ROOT; ?>/public/pictures/desktop/list/list.svg" /></p>
                        <p><img src="<?php echo MVC_ROOT; ?>/public/pictures/desktop/list/grid.svg" /></p>
                        <p><img src="<?php echo MVC_ROOT; ?>/public/pictures/desktop/list/atomic.svg" /></p>
                    </div>
                    <div id="actions">
                        <div class="action"><p><img src="<?php echo MVC_ROOT; ?>/public/pictures/desktop/actions/create_file.svg" /></p></div>
                        <div class="action"><p><img src="<?php echo MVC_ROOT; ?>/public/pictures/desktop/actions/create_folder.svg" /></p></div>
                        <div class="action"><p><img src="<?php echo MVC_ROOT; ?>/public/pictures/desktop/actions/upload.svg" /></p></div>
                    </div>
                </div>
                <div id="rightPanel">
                    <table>
                        	<?php 
                        		/*foreach($arbo as $key => $Arborescence) {
                        			
                        			if($compteur == 4) {
                        				echo "</tr>";
                        				$compteur = 0;
                        				echo "<tr>";
                        			}
                        			if(substr($Arborescence,strlen($Arborescence) - 4 ,1) != ".") {
                        				?> 
                        					<td><img src="./public/pictures/desktop/extensions/folder.svg" /><br /><?php echo $Arborescence ?></td> 
                        				<?php
                        			} else if(substr($Arborescence,strlen($Arborescence) - 3 ,3) == "txt") {
                        				?>
                        					<td><img src="./public/pictures/desktop/extensions/text.svg" /><br /><?php echo $Arborescence ?></td>
                        				<?php 
                        			}else if(substr($Arborescence,strlen($Arborescence) - 3 ,3) == "doc") {
                        				?>
                        					<td><img src="./public/pictures/desktop/extensions/doc.svg" /><br /><?php echo $Arborescence ?></td>
                        				<?php 
                        			} else if(substr($Arborescence,strlen($Arborescence) - 3 ,3) == "png") {
                        				?>
                        					<td><img src="./public/pictures/desktop/extensions/image.svg" /><br /><?php echo $Arborescence ?></td>
                        				<?php 
									} else if(substr($Arborescence,strlen($Arborescence) - 3 ,3) == "cpp") {
                        				?>
                        					<td><img src="./public/pictures/desktop/extensions/code.svg" /><br /><?php echo $Arborescence ?></td>
                        				<?php
									}
                        			$compteur ++;
                        		}*/
                            /*<td><img src="./public/pictures/desktop/extensions/folder.svg" /><br />test</td>
                            <td><img src="./public/pictures/desktop/extensions/folder.svg" /><br />test2</td>
                            <td><img src="./public/pictures/desktop/extensions/code.svg" /><br />source.cpp</td>
                            <td><img src="./public/pictures/desktop/extensions/doc.svg" /><br />document.doc</td>
                            <td><img src="./public/pictures/desktop/extensions/image.svg" /><br />image.png</td>*/?>
                        <tr>
                            <?php 
	                            /*<td><img src="./public/pictures/desktop/extensions/pdf.svg" /><br />rapport.pdf</td>
	                            <td><img src="./public/pictures/desktop/extensions/sound.svg" /><br />sound.mp3</td>
	                            <td><img src="./public/pictures/desktop/extensions/video.svg" /><br />movie.mp4</td>
	                            <td><img src="./public/pictures/desktop/extensions/image.svg" /><br />image2.png</td>*/
                            ?>
                        </tr>
                    </table>
                </div>
                
            </div>
        </section>
    <div id="box" style="display:none"></div>
</body>
<?php
    $_t->getFooter();
?>