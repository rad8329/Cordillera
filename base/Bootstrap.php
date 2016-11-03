<?php

namespace cordillera\base;

use cordillera\middlewares\Config;
use cordillera\middlewares\Exception;

class Bootstrap
{
    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->init($config);
    }

    /**
     * The DB connection is destroyed.
     */
    public function __destruct()
    {
        app()->halt();
    }

    /**
     * @param array $config
     */
    protected function init(array $config)
    {
        Cordillera::$instance = new DI();

        $classmap = $classmap_source = require CORDILLERA_DIR.'classmap.php';

        if (is_file(CORDILLERA_APP_DIR.'config'.DS.'classmap.php')) {
            $classmap = array_merge($classmap, (array) require CORDILLERA_APP_DIR.'config'.DS.'classmap.php');
        }

        // Lazy loading

        Cordillera::$instance->share('config', function () use ($config) {
            return new Config($config);
        });

        Cordillera::$instance->share('session', function () use ($classmap, $classmap_source) {
            return Cordillera::factory($classmap['session'], [
                app()->config->get('session.key'),
                app()->config->get('session.path'),
                app()->config->get('session.lifetime'),
                app()->config->get('session.cookie'),
            ], $classmap_source['session']);
        });

        Cordillera::$instance->share('logger', function () use ($classmap, $classmap_source) {
            return Cordillera::factory($classmap['logger'], [
                app()->config->get('logger', []),
            ], $classmap_source['logger']);
        });

        Cordillera::$instance->share('request', function () use ($classmap, $classmap_source) {
            return Cordillera::factory($classmap['request'], [
                app()->session,
                app()->response,
                app()->config->get('request.csrf', false),
            ], $classmap_source['request']);
        });

        Cordillera::$instance->share('db', function () use ($classmap, $classmap_source) {
            return Cordillera::factory($classmap['db'], [
                app()->config->get('db.dsn'),
                app()->config->get('db.username'),
                app()->config->get('db.password'),
                app()->config->get('db.options', []),
            ], $classmap_source['db']);
        });

        Cordillera::$instance->share('router', function () use ($classmap, $classmap_source) {
            return Cordillera::factory($classmap['router'], [
                app()->request->base_url,
                app()->request->script_name,
                app()->config->get('response.default'),
                $classmap['controller'],
                app()->config->get('router.rules'),
                app()->config->get('router.show_index_file'),
                app()->config->get('router.match_types', []),
            ], $classmap_source['router']);
        });

        Cordillera::$instance->share('auth', function () use ($classmap, $classmap_source) {
            return Cordillera::factory(
                $classmap['auth'],
                [app()->session],
                $classmap_source['auth']
            );
        });

        Cordillera::$instance->share('lang', function () use ($classmap, $classmap_source) {
            return Cordillera::factory(
                $classmap['lang'],
                [app()->config->get('language', 'en')],
                $classmap_source['lang']
            );
        });

        Cordillera::$instance->share('response', function () use ($classmap, $classmap_source) {
            return Cordillera::factory($classmap['response'], [], $classmap_source['response']);
        });
    }

    /**
     * @throws Exception
     */
    public function run()
    {
        try {
            ob_start();
            app()->router->dispatch();
            $output = ob_get_contents();
            ob_end_clean();

            if (!(Cordillera::$exception instanceof Exception)) {
                echo $output;
            } else {
                throw new Cordillera::$exception();
            }
        } catch (\ErrorException $e) {
            app()->exception(new Exception($e->getMessage(), 500, Exception::ERROR, $e));
        } catch (Exception $e) {
            app()->exception($e);
        }

        app()->session->clean('flash');
    }
}
