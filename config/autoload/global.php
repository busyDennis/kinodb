<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

$env = getenv('APPLICATION_ENV');

if ($env == 'production') {
    $db = array(
        'driver' => 'Pdo',
        'dsn' => 'mysql:dbname=heroku_619c2735a0ba77b;host=us-cdbr-iron-east-05.cleardb.net',
        'username' => 'b3ba662782b533',
        'password' => 'b21bd4ba',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        )
    );
} else { // 'development' included
    $db = array(
        'driver' => 'Pdo',
        'dsn' => 'mysql:dbname=kino_db;host=localhost',
        'username' => 'kino_man',
        'password' => 'nopainnogain',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        )
    );
} 


return array(
    'db' => $db
);
// 'service_manager' => array(
// 'factories' => array(
// 'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
// 'ViewJsonStrategy' => 'Zend\Mvc\Service\ViewJsonStrategyFactory',
// ),
// ),

?>