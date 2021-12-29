Opencc
============

A PHP blog system

### Requirements


* `php >= 8.1.0`
* `php-mysqli`
* `mysql >= 5.6 or mariadb >= 10.1.0`
* `composer`
* `a valid smtp server`


Instalation
===========


to install the blog you can use the php install script
``` bash 
# php  script/setup.php [database_host] [database_user] [database_password] [database_name] [root_directory]
```

the script install bdd schema and default configuration , script aslo geneate config file

Use SMTP
===========

You can use SMTP 
change config.inc.php file , change email section
``` php
        'smtp'=>true,
        'smtpsecure'=>true, // if you need  a secure connexion
        'password' => 'xxxxx', // for set smtp password
        'host'=>'xxxx', // smtp HOST
        'port'=>xxx,  // smtp Port
        'secure'=>'tls', // for set tls or ssl connexion
        'from'=>'xxxxx', // set sender address
        'from_name'=>'xxxx',  // set sender name
``` 
        
