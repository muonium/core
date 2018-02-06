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
        'en' => 'English',
	'es' => 'Español',
	'de' => 'Deutsch',
        'fr' => 'Français',
        'it' => 'Italiano',
        'ru' => 'Русский',
	'zh-cn' => '简体中文',
	'pl' => 'Polskie'
    ];

    // Constructor loads user language json
    function __construct($tab = '') {
        if(is_array($tab)) {
            if(array_key_exists('mustBeLogged', $tab)) {
                if($tab['mustBeLogged'] === true && !isset($_SESSION['id'])) {
                    exit(header('Location: '.MVC_ROOT.'/Login'));
                }
            }
            if(array_key_exists('mustBeValidated', $tab)) {
                if($tab['mustBeValidated'] === true && isset($_SESSION['validate'])) {
                    exit(header('Location: '.MVC_ROOT.'/Validate'));
                }
            }
        }
        // Get user language
		$lang = !empty($_COOKIE['lang']) ? htmlentities($_COOKIE['lang']) : DEFAULT_LANGUAGE;
        self::loadLanguage($lang);
    }

	public static function loadLanguage($lang) {
		if(file_exists(DIR_LANGUAGE.$lang.".json")) {
			$_json = file_get_contents(DIR_LANGUAGE.$lang.".json");
			self::$userLanguage = $lang;
		} elseif($lang === DEFAULT_LANGUAGE) {
			exit('Unable to load DEFAULT_LANGUAGE JSON !');
		} else {
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

    // Returns a language selector (select)
    public static function getLanguageSelector() {
        $html = '
			<select onchange="changeLanguage(this.value)">';
        foreach(self::$languages as $iso => $lang) {
            $html .= '
				<option value="'.$iso.'"'.($iso == self::$userLanguage ? ' selected': '').'>'.$lang.'</option>';
        }
        $html .= '</select>';
		return $html;
    }
}
