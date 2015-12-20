<?php

use Gis1\App\Silex\Provider\DoctrineMongoDbProvider;

// Set the error handling
ini_set('display_errors', 1);
error_reporting(-1);

require_once __DIR__.'/vendor/autoload.php';


// Default config.
$config = array(
    'debug' => false,
);

// Initialize Application
$app = new Silex\Application($config);


/*
 * Register providers
 */

$app->register(new Igorw\Silex\ConfigServiceProvider(__DIR__.'/app/config/parameters.yml'));
$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new DoctrineMongoDbProvider(), array(
    'mongodb.options' => array(
        'server' => "mongodb://{$app['parameters']['db_host']}:{$app['parameters']['db_port']}",
        'options' => array(
            'username' => $app['parameters']['db_user'],
            'password' => $app['parameters']['db_password'],
            'db' => $app['parameters']['db_name'],
        ),
    ),
));


/*
 * Register controllers as services
 */

$app['app.building_controller'] = $app->share(
    function () use ($app) {
        return new Gis1\App\Controller\BuildingController($app);
    }
);

$app['app.company_controller'] = $app->share(
    function () use ($app) {
        return new Gis1\App\Controller\CompanyController($app);
    }
);

// Map routes to controllers
include __DIR__.'/app/config/routing.php';
