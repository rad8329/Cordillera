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

namespace cordillera\widgets;

use cordillera\base\Application;
use cordillera\base\interfaces\Display;
use cordillera\middlewares\Exception;
use cordillera\middlewares\Layout;

abstract class WidgetDisplay extends Widget implements Display
{
    /**
     * @var string
     */
    protected $_template = 'widgets/views/layout';

    /**
     * @var Layout
     */
    public $layout;

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::init($config);
        $this->init($config);
    }

    /**
     * @param array $config
     *
     * @throws Exception
     */
    protected function init(array $config = [])
    {
        if (isset($config['layout']) && !($config['layout'] instanceof Layout)) {
            throw new Exception(Application::getLang()->translate('{layout} must be instace of Layout objetc'), 500, Exception::BADARGUMENTS);
        }

        if (isset($config['template']) && !is_string($config['template'])) {
            throw new Exception(Application::getLang()->translate('{template} must be a string'), 500, Exception::BADARGUMENTS);
        }
    }

    /**
     * @param string $template
     *
     * @return string
     */
    public function render()
    {
        ob_start();

        $app_template_file = CORDILLERA_APP_DIR.$this->_template.'.php';
        $cordillera_template_file = CORDILLERA_DIR.$this->_template.'.php';

        if (is_file($app_template_file)) {
            include $app_template_file;
        } elseif (!is_file($app_template_file) && is_file($cordillera_template_file)) {
            include $cordillera_template_file;
        } else {
            throw new Exception(
                Application::getLang()->translate('The widget view %s not found', [$this->_template]),
                500,
                Exception::VIEW
            );
        }

        $output = ob_get_contents();

        ob_end_clean();

        return $output;
    }
}
