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

abstract class Widget
{
    /**
     * @var int
     */
    protected static $counter = 0;

    /**
     * @var string
     */
    public $id;

    /**
     * @var array
     */
    protected $_html_options = [];

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
     * @return WidgetDisplay
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
        }, array_keys($attributes), $attributes));
    }

    protected function run()
    {
    }
}
