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
        $_json = file_get_contents(DIR_LANGUAGE.$this->userLanguage.".json");

        if(!empty($_COOKIE['lang'])) {
            if(file_exists(DIR_LANGUAGE.htmlentities($_COOKIE['lang']).".json")) {
				$_json = file_get_contents(DIR_LANGUAGE.htmlentities($_COOKIE['lang']).".json");
				$this->userLanguage = htmlentities($_COOKIE['lang']);
            }
        }
        $this->txt = json_decode($_json);
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

    public function __get($attr) {
        return $this->$attr;
    }

    public function __set($attr, $val) {
        $this->$attr = $val;
    }
}
