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

function set_language($id_lang, $language=false)
{
   // $language = new Language($id_lang);
    define('_ID_LANG_', $id_lang);
    $_SESSION['id_lang'] = $id_lang;
}

$gl_config = new Config($configArray);
define('_BASE_URL_', _MODE_HTTP_ . $gl_config['webhost']);


define('_DB_PREFIX_', $gl_config['database_master']['prefix']);
$context = Context::getContext();



$collection = new RouteCollection();
$host = $_SERVER['HTTP_HOST'];
$env_host = $gl_config['webhost'];
$collection->attachRoute(new Route('/', [

    'params' => [''],
    '_controller' => 'HomeController::index',
    'methods' => 'GET'
]));
$collection->attachRoute(new Route('/contact', [

    'params' => [''],
    '_controller' => 'ContactController::sendMessage',
    'methods' => 'POST'
]));
$collection->attachRoute(new Route('/contact', [

    'params' => [''],
    '_controller' => 'ContactController::index',
    'methods' => 'GET'
]));

$collection->attachRoute(new Route('/' . _ADMIN_URI_, [

    'params' => [],
    '_controller' => 'AdminDashBoardController::index',
    'methods' => 'GET'
]));

$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/login', [

    'params' => [],
    '_controller' => 'AdminLoginController::index',
    'methods' => 'GET'
]));
$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/login', [

    'params' => [],
    '_controller' => 'AdminLoginController::singin',
    'methods' => 'POST'
]));

$collection->attachRoute(new Route('/[0-9a-z]+-([0-9])+', [

    'params' => ['id_article'],
    '_controller' => 'ArticleController::index',
    'methods' => 'GET'
]));


$router = new Router($collection);
$router->setBasePath('/');
try {
    $route = $router->matchCurrentRequest($context->getCurrentUrl());

}catch (NotFoundException $nf)
{

}

