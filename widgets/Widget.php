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
use cordillera\middlewares\Exception;
use cordillera\middlewares\Layout;
use cordillera\base\interfaces\Display;

abstract class Widget implements Display
{
    /**
     * @var int
     */
    protected static $counter = 0;

    /**
     * @var array
     */
    protected $_html_options = [];

    /**
     * @var string
     */
    protected $_template = 'widgets/views/layout';

    /**
     * @var Layout
     */
    public $layout;

    /**
     * @var string
     */
    public $id;

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->init($config);
    }

    /**
     * @param array $config
     *
     * @throws Exception
     */
    protected function init(array $config = [])
    {
        static::$counter++;

        if (isset($config['layout']) && !($config['layout'] instanceof Layout)) {
            throw new Exception(Application::getLang()->translate('{layout} must be instace of Layout objetc'), 500, Exception::BADARGUMENTS);
        }

        if (isset($config['template']) && !is_string($config['template'])) {
            throw new Exception(Application::getLang()->translate('{template} must be a string'), 500, Exception::BADARGUMENTS);
        }

        if (isset($config['html_options']) && !is_array($config['html_options'])) {
            throw new Exception(Application::getLang()->translate('{html_options} must be an array'), 500, Exception::BADARGUMENTS);
        }

        foreach ($config as $property => $data) {
            if (property_exists($this, $property)) {
                $this->{$property} = $data;
            } elseif (property_exists($this, '_'.$property)) {
                $this->{'_'.$property} = $data;
            }
        }

        if (!$this->id) {
            $this->id = 'widget_'.static::$counter;
        }

        if (isset($this->_html_options['id'])) {
            $this->id = $this->_html_options['id'];
        } else {
            $this->_html_options['id'] = $this->id;
        }
    }

    /**
     * @param array $config
     *
     * @return Widget
     */
    public static function widget(array $config = [])
    {
        $widget = new static($config);
        $widget->run();

        return $widget;
    }

    /**
     * @param array $attributes
     *
     * @return string
     */
    public function bind(array $attributes = [])
    {
        return implode(' ', array_map(function ($attribute, $value) {
                return "{$attribute}=\"{$value}\"";
            }, array_keys($attributes), $attributes)
        );
    }

    /**     
     * @return string
     */
    public function render()
    {
        return $this->fetch();
    }

    /**     
     * @return string
     */
    protected function fetch()
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

    protected function run()
    {
        
    }
}
