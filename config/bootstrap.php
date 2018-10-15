<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();
if (file_exists('../config/settings.inc.php')) {
    require_once('../config/settings.inc.php');
}
define('CONFIG_DIR', $g_base_dir . 'config/');;
error_reporting(E_ALL);
ini_set('display_errors', '1');

// init des constantes
require_once(CONFIG_DIR . 'functions.php');
require_once(CONFIG_DIR . 'define.inc.php');
require_once(VENDOR_DIR . 'autoload.php');
require_once(CLASS_CORE_DIR . 'Autoload.php');

setlocale(LC_TIME, 'fr_FR.UTF8', 'fr.UTF8', 'fr_FR.UTF-8', 'fr.UTF-8');


function __autoload($className)
{


    if (!class_exists($className)) {
        Autoload::getInstance()->load($className);

    }
}

spl_autoload_register('__autoload');

require_once('config.inc.php');

function set_language($id_lang, &$language)
{
    $language = new Language($id_lang);
    define('_ID_LANG_', $id_lang);
    $_SESSION['id_lang'] = $id_lang;
}

$gl_config = new Config($configArray);
define('_BASE_URL_', _MODE_HTTP_ . $gl_config['webhost']);


define('_DB_PREFIX_', $gl_config['database_master']['prefix']);


//if (isset($selected_language) && $selected_language) {
//
//    set_language($selected_language, $language);
//    $base_path = '/' . $language->iso_code . '/';
//
//} else {
//
//    if (!isset($_SESSION['id_lang']) or empty($_SESSION['id_lang'])) {
//
//        set_language($gl_config['id_lang'], $language);
//    } else {
//
//        set_language($_SESSION['id_lang'], $language);
//    }
//
//    $base_path = '/';
//}
//
//define('_DATE_FORMAT_', $language->date_format);
//define('_ISO_LANG_', $language->iso_code);
//define('_HOUR_FORMAT_', $language->hour_format);
$base_path = '/';
//include_once(TRAD_DIR . '/' . _ISO_LANG_ . '.php');

$collection = new RouteCollection();
$host = $_SERVER['HTTP_HOST'];
$env_host = $gl_config['webhost'];




$collection->attachRoute(new Route('/', [

    'params' => ['tour_ref'],
    '_controller' => 'FrontController::home',
    'methods' => 'GET'
]));
$collection->attachRoute(new Route('/[0-9a-z]+-([0-9])+', [

    'params' => ['id_article'],
    '_controller' => 'ArticleController::index',
    'methods' => 'GET'
]));
if ($base_path) {
    define('_BASE_URL_LANG_', _BASE_URL_ . $base_path);
} else {
    define('_BASE_URL_LANG_', _BASE_URL_);
}


$current_page = str_replace($base_path, '/', $_SERVER['REQUEST_URI']);
$current_page = str_replace('//', '/', $current_page);

define('_CURRENT_URL_', $current_page);
$router = new Router($collection);
$router->setBasePath($base_path);
try {
    $route = $router->matchCurrentRequest();

}catch (NotFoundException $nf)
{

}

