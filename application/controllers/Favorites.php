<?php
namespace application\controllers;
use \library\MVC as l;
use \application\models as m;

/*class Favorites extends l\Languages {

    function __construct() {
        parent::__construct(array(
            'mustBeLogged' => true,
            'mustBeValidated' => true
        ));
    }

    function DefaultAction() {
        $mFiles = new m\Files();
        $mFiles->id_owner = $_SESSION['id'];
        $tabFavorites = $mFiles->getFavorites();
        $favorites = '';
        foreach($tabFavorites as $fav) {
            $favorites .= '<span class="file" id="f'.$fav['1'].'" data-folder="'.htmlentities($fav['4']).'" data-title="'.htmlentities($fav['0']).'">'.htmlentities($fav['0']).' ['.$this->showSize($fav['2']).'] - '.$this->txt->User->lastmod.' : '.date('d/m/Y G:i', $fav['3'])."</span>\n";
        }
        require_once(DIR_VIEW.'vFavorites.php');
    }

    function showSize($size, $precision = 2) {
        // $size => size in bytes
        if(!is_numeric($size))
            return 0;
        if($size <= 0)
            return 0;
        $base = log($size, 1024);
        $suffixes = array('', 'K', 'M', 'G', 'T');

        return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
    }
}*/
