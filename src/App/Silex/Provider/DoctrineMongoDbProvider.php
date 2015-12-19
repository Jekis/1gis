<?php

namespace Gis1\App\Silex\Provider;

use Doctrine\Common\EventManager;
use Doctrine\MongoDB\Configuration;
use Doctrine\MongoDB\Connection;
use Silex\Application;
use Silex\ServiceProviderInterface;

class DoctrineMongoDbProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        $app['mongodb.default_options'] = array(
            'server' => 'mongodb://localhost:27017',
            'options' => array(),
        );

        $app['mongodbs.options.initializer'] = $app->protect(function () use ($app) {
            static $initialized = false;

            if ($initialized) {
                return;
            }

            $initialized = true;

            if (!isset($app['mongodbs.options'])) {
                $app['mongodbs.options'] = array('default' => isset($app['mongodb.options']) ? $app['mongodb.options'] : array());
            }

            $tmp = $app['mongodbs.options'];

            foreach ($tmp as $name => &$options) {
                $options = array_replace_recursive($app['mongodb.default_options'], $options);
                if (!isset($app['mongodbs.default'])) {
                    $app['mongodbs.default'] = $name;
                }
            }

            $app['mongodbs.options'] = $tmp;
        });

        $app['mongodbs'] = $app->share(function ($container) {
            $container['mongodbs.options.initializer']();

            $mongodbs = new \Pimple();

            foreach ($container['mongodbs.options'] as $name => $options) {
                if ($container['mongodbs.default'] === $name) {
                    // we use shortcuts here in case the default has been overridden
                    $config = $container['mongodb.config'];
                    $manager = $container['mongodb.event_manager'];
                } else {
                    $config = $container['mongodbs.config'][$name];
                    $manager = $container['mongodbs.event_manager'][$name];
                }
                $mongodbs[$name] = $mongodbs->share(function () use ($options, $config, $manager) {
                    return new Connection($options['server'], $options['options'], $config, $manager);
                });
            }

            return $mongodbs;
        });

        $app['mongodbs.config'] = $app->share(function ($container) {
            $container['mongodbs.options.initializer']();

            $configs = new \Pimple();

            foreach ($container['mongodbs.options'] as $name => $options) {
                $configs[$name] = new Configuration();
//                if (isset($container['logger'])) {
//                    $logger = new Logger($container['logger']);
//                    $configs[$name]->setLoggerCallable(array($logger,'logQuery'));
//                }
            }

            return $configs;
        });

        $app['mongodbs.event_manager'] = $app->share(function ($container) {
            $container['mongodbs.options.initializer']();

            $managers = new \Pimple();

            foreach ($container['mongodbs.options'] as $name => $options) {
                $managers[$name] = new EventManager();
            }

            return $managers;
        });

        // shortcuts for the "first" DB
        $app['mongodb'] = $app->share(function ($container) {
            $mongodbs = $container['mongodbs'];

            return $mongodbs[$container['mongodbs.default']];
        });

        $app['mongodb.config'] = $app->share(function ($container) {
            $mongodbs = $container['mongodbs.config'];

            return $mongodbs[$container['mongodbs.default']];
        });

        $app['mongodb.event_manager'] = $app->share(function ($container) {
            $mongodbs = $container['mongodbs.event_manager'];

            return $mongodbs[$container['mongodbs.default']];
        });
    }
}
