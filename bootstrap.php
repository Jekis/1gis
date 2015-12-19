<?php

// Set the error handling
ini_set('display_errors', 1);
error_reporting(-1);

require_once __DIR__.'/vendor/autoload.php';

// Initialize Application
$app = new Silex\Application();

// Default config.
$config = array(
    'debug' => true,
);

// Register providers,
$app->register(new Igorw\Silex\ConfigServiceProvider(__DIR__.'/app/config/parameters.yml'));
$app->register(new Silex\Provider\ServiceControllerServiceProvider());

/*
 * Register controllers as services
 */
$app['app.building_controller'] = $app->share(
    function () use ($app) {
        return new Gis1\App\Controller\BuildingController();
    }
);

// Map routes to controllers
include __DIR__.'/app/config/routing.php';
