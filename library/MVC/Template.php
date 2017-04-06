<?php
namespace library\MVC;

class Template
{
	private $_title;
	private $_tabCss = "";
	private $_tabJs = "";
	private $_pathCss = "/public/css/";
	private $_pathJs = "/public/js/";

	private $_script = "";
    private $_meta = "";

	function __construct($title)
	{
		$this->_title = $title;
	}

    function addCss($tabCss) {
        $this->_tabCss[$tabCss]['Fichier'] = MVC_ROOT.$this->_pathCss.$tabCss.".css";
    }

    function addJs($tabJs) {
        $this->_tabJs[$tabJs]['Fichier'] = MVC_ROOT.$this->_pathJs.$tabJs.".js";
    }

    function addScript($type,$contenu) {
        $this->_script[$type]["Type"] = $type;
        $this->_script[$type]["Contenu"] = $contenu;
    }

    function addMeta($name,$content) {
        $this->_meta[$name]["Name"] = $name;
        $this->_meta[$name]["Content"] = $content;
    }

	function getHeader() {
		echo '
		<!DOCTYPE html>
		<html>
			<head>
				<meta charset="utf-8">
				<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
				<title>'.$this->_title.'</title>
                <link rel="icon" type="image/x-icon" href="'.MVC_ROOT.'/public/pictures/favicon_small.ico" />
				<link rel="icon" type="image/png" href="'.MVC_ROOT.'/public/pictures/favicon_small.png" />
				<base href="'.MVC_ROOT.'/">
		'."\n";

		echo '<link rel="stylesheet" type="text/css" href="'.MVC_ROOT.$this->_pathCss.'font-awesome/css/font-awesome.min.css" />';
		if(!empty($this->_tabCss)) {
            foreach($this->_tabCss as $id => $fichier)
                echo "\t".'<link rel="stylesheet" type="text/css" href="'.$fichier['Fichier'].'?v='.VERSION.'" />'."\n";
		}

		echo '<script type="text/javascript" id="language-js" src="'.MVC_ROOT.$this->_pathJs.'language.js?v='.VERSION.'"></script>'."\n";
		if(!empty($this->_tabJs)) {
            foreach($this->_tabJs as $id => $fichier)
                echo '<script type="text/javascript" src="'.$fichier['Fichier'].'?v='.VERSION.'"></script>'."\n";
		}

        if(!empty($this->_script)) {
            foreach($this->_script as $id => $fichier)
                echo "\t".'<script type="'.$fichier["Type"].'"> '.$fichier["Contenu"].'</script> '."\n";
        }

        if(!empty($this->_meta)) {
            foreach($this->_meta as $id => $fichier)
                echo "\t".'<meta name="'.$fichier["Name"].'" content="'.$fichier["Content"].'" /> '."\n";
        }
		echo '
		<noscript><div class="pasDeJs"><div class="txtPasDeJs">
		Vous devez activer les scripts Javascript pour visualiser le contenu de l\'application.</div></div></noscript>'."\n";

		echo '</head>'."\n";
	}

	function getRegisteredMenu($strings) {
		echo '
		<div id="user">
			<p><a href="'.MVC_ROOT.'/Bug"><img src="'.MVC_ROOT.'/public/pictures/header/bug.svg" /><br />'.htmlentities($strings->bug).'</a></p>
			<p><a href="https://github.com/muonium/core/wiki/%5BHELP%5D-:-User-Experience" target="_blank"><img src="'.MVC_ROOT.'/public/pictures/header/help.svg" /><br />'.htmlentities($strings->help).'</a></p>
			<p><img src="'.MVC_ROOT.'/public/pictures/header/settings.svg" /><br />'.htmlentities($strings->settings).'</p>
			<p><a href="'.MVC_ROOT.'/Profile"><img src="'.MVC_ROOT.'/public/pictures/header/user.svg" /><br />'.htmlentities($strings->profile).'</a></p>
			<p><a href="'.MVC_ROOT.'/Logout"><img src="'.MVC_ROOT.'/public/pictures/header/user.svg" /><br />'.htmlentities($strings->logout).'</a></p>
		</div>';
	}

	function getFooter() {
        echo '</html>';
    }
};
