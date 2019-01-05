<?php
/**
 * Created by PhpStorm.
 * User: worf
 * Date: 04/01/19
 * Time: 23:36
 */


if ($argc !=9 )
{
    echo ("setup.php [database_host] [database_user] [database_password] [database_name] [database_prefix] [root_directory] [site_host] [site_name]");

}else {

    $_SERVER = [];
    $_SERVER['REQUEST_URI'] = '';
    $_SERVER['HTTP_HOST'] = '';
    $_SERVER['REQUEST_METHOD'] = '';
    $_SERVER['SCRIPT_NAME'] = '';
    $database_host = $argv[1];
    $database_user = $argv[2];
    $database_password = $argv[3];
    $database_name = $argv[4];
    $database_prefix = $argv[5];

    $root_directory = $argv[6];
    $site_host = $argv[7];
    $site_name = $argv[8];

    chdir($root_directory . '/public');
    $connexion = mysqli_connect($database_host, $database_user, $database_password, $database_name);
    if (!$connexion) {
        throw  new Exception("Not connexion to bdd");
    }
    $sql = file_get_contents($root_directory . "/script/schema/openccblog.sql");
    mysqli_multi_query($connexion, $sql);
    do {
        /* sStockage du premier résultat */
        if ($result = mysqli_store_result($connexion)) {
            while ($row = mysqli_fetch_row($result)) {
            }
            mysqli_free_result($result);
        }
        /* Affichage d'une séparation */
        if (mysqli_more_results($connexion)) {
        }
    } while (mysqli_next_result($connexion));
### Create Lang ####

    $sql = "INSERT INTO `" . $database_prefix . "lang` (`id_lang`, `name`, `iso`, `date_add`, `local`) VALUES (NULL, 'français', 'fr', '0000-00-00 00:00:00', 'fr_FR');
INSERT INTO  `" . $database_prefix . "lang` (`id_lang`, `name`, `iso`, `date_add`, `local`) VALUES (NULL, 'anglais', 'en', NULL, 'en_US ');
";

    mysqli_multi_query($connexion, $sql);

    do {
        /* sStockage du premier résultat */
        if ($result = mysqli_store_result($connexion)) {
            while ($row = mysqli_fetch_row($result)) {
            }
            mysqli_free_result($result);
        }
        /* Affichage d'une séparation */
        if (mysqli_more_results($connexion)) {
        }
    } while (mysqli_next_result($connexion));

    $htaccess = '
    Options +FollowSymLinks
RewriteEngine On



RewriteRule ^picture/([0-9]+)/([0-9]+)/(.+) resize.php?width=$1&file=$3&height=$2
RewriteRule ^picture/([a-z]+)/(.+) resize.php?size=$1&file=$2


RewriteCond %{REQUEST_FILENAME} !-f

RewriteRule ^(.*)$ index.php [NC,L]
    
    ';
    file_put_contents($root_directory . "/public/.htaccess", $htaccess);

## Create Confif file
    $settings = '<?php
$g_base_dir = getcwd() . \'/../\';
define(\'_LOCAL_ZONE_\', \'Europe/Paris\');
define(\'_ADMIN_URI_\', \'admin\');
define(\'_MODE_HTTP_\', \'http://\');
';
    file_put_contents($root_directory . "/config/settings.inc.php", $settings);

    $config = '<?php 
$configArray = array(
    "contactMail"=>"",
    \'id_lang\' => 1,
    \'webhost\' => \'' . $site_host . '\',
    \'email\' => array(
        \'smtp\'=>false,
    ),
    \'database_master\' => array(
        \'prefix\' => \'' . $database_prefix . '\',
        \'adapter\' => \'mysqli\',

        \'params\' => array(
            \'host\' => \'' . $database_host . '\',
            \'username\' => \'' . $database_user . '\',
            \'password\' => \'' . $database_password . '\',
            \'dbname\' => \'' . $database_name . '\'
        )
    )
);

';
    file_put_contents($root_directory . "/config/config.inc.php", $config);

    require $root_directory . "/config/bootstrap.php";

    $configuration = new Configuration();
    $configuration->key = "_CV_PATH_";
    $configuration->description = "Lien vers le cv";
    $configuration->save();

    $configuration = new Configuration();
    $configuration->key = "_SITE_TITLE_";
    $configuration->value_lang = '$site_name';
    $configuration->description = "Titre du site";

    $configuration->save();

    $configuration = new Configuration();
    $configuration->key = "_SITE_BASE_LINE_";
    $configuration->description = "Sous titre";

    $configuration->save();


    $configuration = new Configuration();
    $configuration->key = "_SITE_NAME_";
    $configuration->description = "Nom du site";

    $configuration->save();


    $configuration = new Configuration();
    $configuration->key = "_LIMIT_HOMEPAGE_POST_";
    $configuration->value = 3;
    $configuration->description = "nombre de post home page";
    $configuration->save();


    $configuration = new Configuration();
    $configuration->key = "_LIMIT_CATEGORY_POST_";
    $configuration->value = 3;
    $configuration->description = "nombre de post category";
    $configuration->save();

    $configuration = new Configuration();
    $configuration->key = "_LIMIT_SIDEBAR_POST_";
    $configuration->value = 3;
    $configuration->description = "Nombre de post widget derniers article";
    $configuration->save();
    $password =  bin2hex(random_bytes(8));
    $admin =new Administrator();
    $admin->mail = "root";
    $admin->password = $password;
    $admin->save();

    echo "A admin has been created with login root and password $password\n We need delete this admin for production\n";
    echo("Blog is install\n");
}