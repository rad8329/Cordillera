<?php

/*
 * This file is part of the Cordillera framework.
 *
 * (c) Robert Adrián Díaz <rad8329@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Made with love in Medellín
 */

namespace cordillera\base;

use cordillera\middlewares\Config;
use cordillera\middlewares\Exception;
use cordillera\middlewares\Response;

class  Bootstrap
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
        Application::halt();
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
                Application::getConfig()->get('session.key'),
                Application::getConfig()->get('session.path'),
                Application::getConfig()->get('session.lifetime'),
                Application::getConfig()->get('session.cookie'),
            ], $classmap_source['session']);
        });

        Cordillera::$instance->share('logger', function () use ($classmap, $classmap_source) {
            return Cordillera::factory($classmap['logger'], [
                Application::getConfig()->get('logger', []),
            ], $classmap_source['logger']);
        });

        Cordillera::$instance->share('request', function () use ($classmap, $classmap_source) {
            return Cordillera::factory($classmap['request'], [
                Application::getSession(),
                Application::getConfig()->get('request.csrf', false),
            ], $classmap_source['request']);
        });

        Cordillera::$instance->share('db', function () use ($classmap, $classmap_source) {
            return Cordillera::factory($classmap['db'], [
                Application::getConfig()->get('db.dsn'),
                Application::getConfig()->get('db.username'),
                Application::getConfig()->get('db.password'),
                Application::getConfig()->get('db.options', []),
            ], $classmap_source['db']);
        });

        Cordillera::$instance->share('router', function () use ($classmap, $classmap_source) {
            return Cordillera::factory($classmap['router'], [
                Application::getRequest()->base_url,
                Application::getRequest()->script_name,
                Application::getConfig()->get('response.default'),
                $classmap['controller'],
                Application::getConfig()->get('router.rules'),
                Application::getConfig()->get('router.show_index_file'),
                Application::getConfig()->get('router.match_types', []),
            ], $classmap_source['router']);
        });

        Cordillera::$instance->share('auth', function () use ($classmap, $classmap_source) {
            return Cordillera::factory($classmap['auth'],
                [Application::getSession()], $classmap_source['auth']);
        });

        Cordillera::$instance->share('lang', function () use ($classmap, $classmap_source) {
            return Cordillera::factory($classmap['lang'],
                [Application::getConfig()->get('language', 'en')],
                $classmap_source['lang']);
        });
    }

    /**
     * @throws Exception
     */
    public function run()
    {
        try {
            ob_start();
            Application::getRouter()->dispatch();
            $output = ob_get_contents();
            ob_end_clean();

            //Application::getLogger()->debug("test",['logger'=>Application::getLogger()]);
            //dumpx(Application::getLogger()->);
           // dumpx(Application::getLogger());
            if (!(Cordillera::$exception instanceof Exception)) {
                echo $output;
            } else {
                throw new Cordillera::$exception();
            }
        } catch (\ErrorException $e) {
            Response::exception(new Exception($e->getMessage(), 500, Exception::ERROR, $e));
        } catch (Exception $e) {
            Response::exception($e);
        }

        Application::getSession()->clean('flash');
    }
}
