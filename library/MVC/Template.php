<?php
namespace library\MVC;

class Template
{
	private $_title;
	/*
	 * Contient le titre de la page
	 * @var string
	 * */
	private $_tabCss = "";
	/*
	 * Variable contenant le nom du fichier css
	 * @var string
	 * */
	private $_tabJs = "";
	/*
	 * Variable contenant le nom du fichier js 
	 * @var string
	 * */
	private $_pathCss = "/public/css/";
	/*
	 * Chemin des fichiers javascript
	* @var string
	* */
	private $_pathJs = "/public/js/";
	/*
	 * chemin des fichiers CSS
	* @var string
	* */
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
		<html lang="fr">
			<head>
				<meta charset="utf-8">
				<title>'.$this->_title.'</title>
                <link rel="icon" type="image/x-icon" href="'.MVC_ROOT.'/public/pictures/favicon_small.ico" />
                <link rel="icon" type="image/png" href="'.MVC_ROOT.'/public/pictures/favicon_small.png" />  
		'."\n";

		if(!empty($this->_tabCss)) {
            foreach($this->_tabCss as $id => $fichier)
                echo "\t".'<link rel="stylesheet" type="text/css" href="'.$fichier['Fichier'].'" />'."\n";
		}
		
		echo '<script type="text/javascript" src="'.MVC_ROOT.$this->_pathJs.'language.js"></script>'."\n";
		if(!empty($this->_tabJs)) {
            foreach($this->_tabJs as $id => $fichier)
                echo '<script type="text/javascript" src="'.$fichier['Fichier'].'"></script>'."\n";
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

	function getFooter()
    	{
        	echo '</html>';
    	}
};
