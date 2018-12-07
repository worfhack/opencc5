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
$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/logout', [

    'params' => [],
    '_controller' => 'AdminLoginController::logout',
    'methods' => 'GET'
]));
$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/login', [

    'params' => [],
    '_controller' => 'AdminLoginController::singin',
    'methods' => 'POST'
]));











$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/media' , [

    'params'     => ['link_rewrite'] ,
    '_controller' => 'AdminMediaController::list',
    'methods' => 'GET'
]));
$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/media/add' , [

    'params'     => ['link_rewrite'] ,
    '_controller' => 'AdminMediaController::form',
    'methods' => 'GET'
]));
$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/media/add' , [

    'params'     => ['link_rewrite'] ,
    '_controller' => 'AdminMediaController::add',
    'methods' => 'POST'
]));
$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/media/edit/([0-9]+)' , [

    'params'     => ['id'] ,
    '_controller' => 'AdminMediaController::form',
    'methods' => 'GET'
]));

$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/media/remove/([0-9]+)' , [

    'params'     => ['id'] ,
    '_controller' => 'AdminMediaController::remove',
    'methods' => 'GET'
]));









$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/author' , [

    'params'     => ['link_rewrite'] ,
    '_controller' => 'AdminAuthorController::list',
    'methods' => 'GET'
]));
$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/author/add' , [

    'params'     => ['link_rewrite'] ,
    '_controller' => 'AdminAuthorController::form',
    'methods' => 'GET'
]));
$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/author/add' , [

    'params'     => ['link_rewrite'] ,
    '_controller' => 'AdminAuthorController::add',
    'methods' => 'POST'
]));
$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/author/edit/([0-9]+)' , [

    'params'     => ['id'] ,
    '_controller' => 'AdminAuthorController::form',
    'methods' => 'GET'
]));

$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/author/remove/([0-9]+)' , [

    'params'     => ['id'] ,
    '_controller' => 'AdminAuthorController::remove',
    'methods' => 'GET'
]));
$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/author/edit/([0-9]+)' , [

    'params'     => [ 'id'] ,
    '_controller' => 'AdminAuthorController::edit',
    'methods' => 'POST'
]));





$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/configuration' , [

    'params'     => ['link_rewrite'] ,
    '_controller' => 'AdminConfigurationController::list',
    'methods' => 'GET'
]));
$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/configuration/add' , [

    'params'     => ['link_rewrite'] ,
    '_controller' => 'AdminConfigurationController::form',
    'methods' => 'GET'
]));
$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/configuration/add' , [

    'params'     => ['link_rewrite'] ,
    '_controller' => 'AdminConfigurationController::add',
    'methods' => 'POST'
]));
$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/configuration/edit/([0-9]+)' , [

    'params'     => ['id'] ,
    '_controller' => 'AdminConfigurationController::form',
    'methods' => 'GET'
]));

$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/configuration/remove/([0-9]+)' , [

    'params'     => ['id'] ,
    '_controller' => 'AdminConfigurationController::remove',
    'methods' => 'GET'
]));
$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/configuration/edit/([0-9]+)' , [

    'params'     => [ 'id'] ,
    '_controller' => 'AdminConfigurationController::edit',
    'methods' => 'POST'
]));







$collection->attachRoute(new Route('/category/([0-9a-z\-\/]+)', [

    'params' => ['rewrite'],
    '_controller' => 'CategoryController::index',
    'methods' => 'GET'
]));


$collection->attachRoute(new Route('/[0-9a-z\-]+-([0-9]+)', [

    'params' => ['id_article'],
    '_controller' => 'ArticleController::index',
    'methods' => 'GET'
]));







$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/category' , [

    'params'     => ['link_rewrite'] ,
    '_controller' => 'AdminCategoryController::list',
    'methods' => 'GET'
]));
$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/category/add' , [

    'params'     => ['link_rewrite'] ,
    '_controller' => 'AdminCategoryController::form',
    'methods' => 'GET'
]));
$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/category/add' , [

    'params'     => ['link_rewrite'] ,
    '_controller' => 'AdminCategoryController::add',
    'methods' => 'POST'
]));
$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/category/edit/([0-9]+)' , [

    'params'     => ['id'] ,
    '_controller' => 'AdminCategoryController::form',
    'methods' => 'GET'
]));

$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/category/remove/([0-9]+)' , [

    'params'     => ['id'] ,
    '_controller' => 'AdminCategoryController::remove',
    'methods' => 'GET'
]));
$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/category/edit/([0-9]+)' , [

    'params'     => [ 'id'] ,
    '_controller' => 'AdminCategoryController::edit',
    'methods' => 'POST'
]));






$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/article' , [

    'params'     => ['link_rewrite'] ,
    '_controller' => 'AdminArticleController::list',
    'methods' => 'GET'
]));
$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/article/add' , [

    'params'     => ['link_rewrite'] ,
    '_controller' => 'AdminArticleController::form',
    'methods' => 'GET'
]));
$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/article/add' , [

    'params'     => ['link_rewrite'] ,
    '_controller' => 'AdminArticleController::add',
    'methods' => 'POST'
]));
$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/article/edit/([0-9]+)' , [

    'params'     => ['id'] ,
    '_controller' => 'AdminArticleController::form',
    'methods' => 'GET'
]));

$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/article/remove/([0-9]+)' , [

    'params'     => ['id'] ,
    '_controller' => 'AdminArticleController::remove',
    'methods' => 'GET'
]));
$collection->attachRoute(new Route('/' . _ADMIN_URI_.'/article/edit/([0-9]+)' , [

    'params'     => [ 'id'] ,
    '_controller' => 'AdminArticleController::edit',
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

