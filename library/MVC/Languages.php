<?php
namespace library\MVC;

class Languages {
    // This class is called by all controllers (with "extends")
    // It allows to use different languages

    // $txt contains user language json
    public $txt;

    protected $userLanguage = DEFAULT_LANGUAGE;

    // Available languages
    protected $languages = array(
        array('en', 'English'),
        array('fr', 'Français'),
        array('it', 'Italiano'),
        array('ru', 'Русский'),
		array('de','Deutsch')
    );

    // Constructor loads user language json
    function __construct($tab = '')
    {
        function loadLanguage($t, $lang) {
            if(file_exists(DIR_LANGUAGE.$lang.".json")) {
                $_json = file_get_contents(DIR_LANGUAGE.$lang.".json");
				$t->userLanguage = $lang;
            }
            elseif($lang === DEFAULT_LANGUAGE) {
                exit('Unable to load DEFAULT_LANGUAGE JSON !');
            }
            else {
                loadLanguage($t, DEFAULT_LANGUAGE);
            }

            $t->txt = json_decode($_json);
            if(json_last_error() !== 0) {
                if($lang === DEFAULT_LANGUAGE) {
                    exit('Error in the DEFAULT_LANGUAGE JSON !');
                }
                loadLanguage($t, DEFAULT_LANGUAGE);
            }
            return true;
        }

        if(is_array($tab)) {
            if(array_key_exists('mustBeLogged', $tab)) {
                if($tab['mustBeLogged'] == true && empty($_SESSION['id'])) {
                    exit(header('Location: '.MVC_ROOT.'/Login'));
                }
            }
            if(array_key_exists('mustBeValidated', $tab)) {
                if($tab['mustBeValidated'] == true && !empty($_SESSION['validate'])) {
                    exit(header('Location: '.MVC_ROOT.'/Validate'));
                }
            }
        }

        // Get user language
        if(!empty($_COOKIE['lang'])) {
            loadLanguage($this, htmlentities($_COOKIE['lang']));
        }
        else {
            loadLanguage($this, DEFAULT_LANGUAGE);
        }
    }

    // Generate a language selector (select)
    function getLanguageSelector()
    {
        echo '<select onchange="changeLanguage(this.value)">';
        for($i=0;$i<count($this->languages);$i++) {
            echo '<option value="'.$this->languages[$i][0].'"';
            if($this->languages[$i][0] == $this->userLanguage)
                echo ' selected';
            echo '>'.$this->languages[$i][1].'</option>';
        }
        echo '</select>';
    }

	function showSize($size, $precision = 2) {
		// $size => size in bytes
		if(!is_numeric($size)) return 0;
		if($size <= 0) return 0;
		$base = log($size, 1000);
		$suffixes = array_values((array)$this->txt->Units);

		return round(pow(1000, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
	}

    public function __get($attr) {
        return $this->$attr;
    }

    public function __set($attr, $val) {
        $this->$attr = $val;
    }
}
