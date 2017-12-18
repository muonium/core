<?php
namespace library\MVC;

class Languages {
    // This class is called by all controllers (with "extends")
    // It allows to use different languages

    // $txt contains user language json
    public static $txt = null;
    public static $userLanguage = DEFAULT_LANGUAGE;

    // Available languages
    public static $languages = [
        ['en', 'English'],
		['de', 'Deutsch'],
        ['fr', 'Français'],
        ['it', 'Italiano'],
        ['ru', 'Русский']
    ];

	public static function loadLanguage($lang) {
		if(file_exists(DIR_LANGUAGE.$lang.".json")) {
			$_json = file_get_contents(DIR_LANGUAGE.$lang.".json");
			self::$userLanguage = $lang;
		}
		elseif($lang === DEFAULT_LANGUAGE) {
			exit('Unable to load DEFAULT_LANGUAGE JSON !');
		}
		else {
			self::loadLanguage(DEFAULT_LANGUAGE);
		}

		self::$txt = json_decode($_json);
		if(json_last_error() !== 0) {
			if($lang === DEFAULT_LANGUAGE) {
				exit('Error in the DEFAULT_LANGUAGE JSON !');
			}
			self::loadLanguage(DEFAULT_LANGUAGE);
		}
		return true;
	}

    // Constructor loads user language json
    function __construct($tab = '') {
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
		$lang = !empty($_COOKIE['lang']) ? htmlentities($_COOKIE['lang']) : DEFAULT_LANGUAGE;
        self::loadLanguage($lang);
    }

    // Generate a language selector (select)
    protected function getLanguageSelector() {
        echo '<select onchange="changeLanguage(this.value)">';
        for($i = 0; $i < count(self::$languages); $i++) {
            echo '<option value="'.self::$languages[$i][0].'"';
            if(self::$languages[$i][0] == self::$userLanguage) echo ' selected';
            echo '>'.self::$languages[$i][1].'</option>';
        }
        echo '</select>';
    }
}
