<?php
namespace library\MVC;

class Template {
	private $_title = null;
	private $_css = [];
	private $_js = [];
	private $_customHead = null;
	private $_customHeader = null;
	private $_sidebar = false;

	/* Avoid loading version from browser cache when a new version is released */
	private $_path = MVC_ROOT.'/public/version/'.VERSION.'/';

	private $_script = [];
    private $_meta = [];

	function __construct($title = null) {
		// title (string) - Document title
		$this->_title = $title;
	}

    public function addCss($files) {
		// files (string or array) - CSS files needed
        $this->_css = array_merge($this->_css, (array)$files);
		return $this;
    }

    public function addJs($files) {
		// files (string or array) - JS files needed
        $this->_js = array_merge($this->_js, (array)$files);
		return $this;
    }

    public function addScript($content) {
		// content (string)
        $this->_script[] = $content;
		return $this;
    }

    public function addMeta($name, $content) {
		// name (string), content (string)
		$this->_meta[] = ['name' => $name, 'content' => $content];
		return $this;
    }

	public function setCustomHead($content) {
		// content (string)
		$this->_customHead = $content;
		return $this;
	}

	public function setCustomHeader($content) {
		// content (string)
		$this->_customHeader = $content;
		return $this;
	}

	public function getHead() {
		$html = '
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
		<title>'.$this->_title.'</title>
        <link rel="icon" type="image/x-icon" href="'.MVC_ROOT.'/public/pictures/favicon_small.ico">
		<link rel="icon" type="image/png" href="'.MVC_ROOT.'/public/pictures/favicon_small.png">
		<base href="'.MVC_ROOT.'/">
';
		/* Necessary stuff - jQuery, Roboto font and font-awesome can be cached */
		$html .= '
		<link rel="stylesheet" type="text/css" href="'.MVC_ROOT.'/public/css/font-awesome/css/font-awesome.min.css">
		<link rel="stylesheet" type="text/css" href="'.MVC_ROOT.'/public/css/fonts/Roboto.css">
		<link rel="stylesheet" type="text/css" href="'.MVC_ROOT.'/public/css/fonts/RobotoMono.css">
		<link rel="stylesheet" type="text/css" href="'.MVC_ROOT.'/public/css/fonts/OpenSans.css">
		<script src="'.MVC_ROOT.'/public/js/jquery-3.2.1.min.js"></script>
		<script src="'.$this->_path.'js/language.js" id="language-js"></script>
';
/* For development purpose */
if(isset($_GET['dark'])) {
	$html .= '<link rel="stylesheet" type="text/css" href="'.$this->_path.'css/2018/dark.css">';
} else {
	$html .= '<link rel="stylesheet" type="text/css" href="'.$this->_path.'css/2018/light.css">';
}
/* */
		foreach($this->_meta as $meta) {
			$html .= '
		<meta name="'.$meta['name'].'" content="'.$meta['content'].'">';
		}

        foreach($this->_css as $file) {
            $html .= '
		<link rel="stylesheet" type="text/css" href="'.$this->_path.'css/'.$file.'.css">';
		}

        foreach($this->_js as $file) {
            $html .= '
		<script src="'.$this->_path.'js/'.$file.'.js"></script>';
		}

        foreach($this->_script as $script) {
            $html .= '
		<script>'.$script.'</script>';
		}

		$html .= '
		'.$this->_customHead.'
	</head>
';
		return $html;
	}

	public function getHeader() {
		$html = '
	<body>
		<header>
	        <div id="logo">
	            <a href="'.URL_APP.'" target="_blank">
	                <img src="public/pictures/logos/muonium_H_01.png" title="'.Languages::$txt->Global->home.'" alt="'.Languages::$txt->Global->home.'">
	            </a>
	        </div>
	        <div id="language-selector">
	            '.Languages::getLanguageSelector().'
	        </div>
		';
		if(isset($_SESSION['id'])) {
			$html .= '<a href="Logout" class="logout"><i class="fa fa-sign-out" aria-hidden="true"></i></a>';
		}
	    $html .= '</header>
';
		$html .= $this->_customHeader;
		return $html;
	}

	public function getFooter() {
		$html = '';
		if($this->_sidebar) $html .= '</div>';
        $html .= '
	</body>
</html>';
		return $html;
    }

	public function getSidebar() {
		$html = '
		<div id="main">
			<div class="sidebar">
				<ul>
	                <li>
	                    <a href="User">
							<i class="fa fa-file" aria-hidden="true"></i>
	                    </a>
	                </li>
					<li>
						<a href="User">
							<i class="fa fa-trash" aria-hidden="true"></i>
						</a>
					</li>
	                <li>
	                    <a onclick="Transfers.toggle()">
							<i class="fa fa-exchange" aria-hidden="true"></i>
	                    </a>
	                </li>
					<li>
	                    <a href="Bug">
							<i class="fa fa-bug" aria-hidden="true"></i>
						</a>
	                </li>
					<li>
	                    <a href="Profile" class="selected">
							<i class="fa fa-cog" aria-hidden="true"></i>
						</a>
	                </li>
	            </ul>
			</div>
		';
		return $html;
	}
}
