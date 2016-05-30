<?php
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
	private $_PathCss = "/public/css/";
	/*
	 * Chemin des fichiers javascript
	* @var string
	* */
	private $_PathJs = "/public/js/";
	/*
	 * chemin des fichiers CSS
	* @var string
	* */
	private $_Script = "";
    private $_Meta = "";
    
	function __construct($title)
	{
		$this->_title = $title;
	}
    
    function addCss($tabCss) {
        $this->_tabCss[$tabCss]['Fichier'] = MVC_ROOT.$this->_PathCss.$tabCss.".css";
    }
    
    function addJs($tabJs) {
        $this->_tabJs[$tabJs]['Fichier'] = MVC_ROOT.$this->_PathJs.$tabJs.".js";
    }
    
    function addScript($type,$contenu) {
        $this->_Script[$type]["Type"] = $type;
        $this->_Script[$type]["Contenu"] = $contenu;
    }
    
    function addMeta($name,$content) {
        $this->_Meta[$name]["Name"] = $name;
        $this->_Meta[$name]["Content"] = $content;
    }
    
	function getHeader() {
        
		echo '
		<!DOCTYPE html>
		<html lang="fr">
			<head>
				<meta charset="utf-8">
				<title>'.$this->_title.'</title>
                <link rel="icon" type="image/png" href="'.MVC_ROOT.'/public/pictures/favicon.png" />  
		';

		if(!empty($this->_tabCss)) {
            foreach($this->_tabCss as $id => $fichier)
                echo "\t".'<link rel="stylesheet" type="text/css" href="'.$fichier['Fichier'].'" />'."\n";
		}
		
		
		if(!empty($this->_tabJs)) {
            foreach($this->_tabJs as $id => $fichier)
                echo '<script type="text/javascript" src="'.$fichier['Fichier'].'"></script>';
		}
        
        if(!empty($this->_Script)) {
            foreach($this->_Script as $id => $fichier) 
                echo "\t".'<script type="'.$fichier["Type"].'"> '.$fichier["Contenu"].'</script> '."\n";
        }
		
        if(!empty($this->_Meta)) {
            foreach($this->_Meta as $id => $fichier) 
                echo "\t".'<meta name="'.$fichier["Name"].'" content="'.$fichier["Content"].'" /> '."\n";
        }
		echo '
		<noscript><div class="pasDeJs"><div class="txtPasDeJs">
		Vous devez activer les scripts Javascript pour visualiser le contenu de l\'application.</div></div></noscript>'."\n";
		
		echo '</head>';
	}

	function getFooter()
    	{
        	echo '</html>';
    	}
}
?>