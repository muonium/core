<?php
namespace library\MVC;

class Template
{
	private $_title;
	private $_tabCss = [];
	private $_tabJs = [];
	/* Do not load version from cache when a new version is released */
	private $_path = MVC_ROOT.'/public/version/'.VERSION.'/';

	private $_script = [];
    private $_meta = [];

	function __construct($title) {
		$this->_title = $title;
	}

    function addCss($file) {
        $this->_tabCss[] = $file;
    }

    function addJs($file) {
        $this->_tabJs[] = $file;
    }

    function addScript($type, $content) {
        $this->_script[] = ["type" => $type, "content" => $content];
    }

    function addMeta($name, $content) {
		$this->_meta[] = ["name" => $name, "content" => $content];
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
';

		/* Necessary stuff - jQuery, Roboto font and font-awesome can be cached */
		echo "\t".'<link rel="stylesheet" type="text/css" href="'.MVC_ROOT.'/public/css/font-awesome/css/font-awesome.min.css" />'."\n";
		echo "\t".'<link rel="stylesheet" type="text/css" href="'.MVC_ROOT.'/public/css/fonts/roboto.css" />'."\n";
		echo "\t".'<script type="text/javascript" src="'.MVC_ROOT.'/public/js/jquery-3.2.1.min.js"></script>'."\n";
		echo "\t".'<script type="text/javascript" id="language-js" src="'.$this->_path.'js/language.js"></script>'."\n";

		if(count($this->_tabCss) > 0) {
            foreach($this->_tabCss as $file)
                echo "\t".'<link rel="stylesheet" type="text/css" href="'.$this->_path.'css/'.$file.'.css" />'."\n";
		}

		if(count($this->_tabJs) > 0) {
            foreach($this->_tabJs as $file)
                echo "\t".'<script type="text/javascript" src="'.$this->_path.'js/'.$file.'.js"></script>'."\n";
		}

        if(count($this->_script) > 0) {
            foreach($this->_script as $script)
                echo "\t".'<script type="'.$script['type'].'"> '.$script['content'].'</script> '."\n";
        }

        if(count($this->_meta) > 0) {
            foreach($this->_meta as $meta)
                echo "\t".'<meta name="'.$meta['name'].'" content="'.$meta['content'].'" /> '."\n";
        }

		echo '</head>'."\n";
	}

	function getFooter() {
        echo '</html>';
    }
};
