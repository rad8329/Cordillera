<?php

namespace cordillera\widgets;

use cordillera\base\interfaces\Display;
use cordillera\middlewares\Exception;

abstract class Widget implements Display
{
    /**
     * @var int
     */
    protected static $_counter = 0;

    /**
     * @var array
     */
    protected $_html_options = [];

    /**
     * @var \Closure
     */
    protected $_renderer;

    /**
     * @var string
     */
    protected $_template = 'widgets/views/layout';

    /**
     * @var string
     */
    public $id;

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        static::$_counter++;
        $this->setup($config);
    }

    /**
     * @param array $config
     *
     * @throws Exception
     */
    protected function setup(array $config = [])
    {
        if (isset($config['renderer']) && (!($config['renderer'] instanceof \Closure) && !is_callable($config['renderer']))) {
            throw new Exception(translate('{renderer} must be callable'), 500, Exception::BADARGUMENTS);
        }

        if (isset($config['template']) && !is_string($config['template'])) {
            throw new Exception(translate('{template} must be a string'), 500, Exception::BADARGUMENTS);
        }

        if (isset($config['html_options']) && !is_array($config['html_options'])) {
            throw new Exception(translate('{html_options} must be an array'), 500, Exception::BADARGUMENTS);
        }

        foreach ($config as $property => $data) {
            if (property_exists($this, $property)) {
                $this->{$property} = $data;
            } elseif (property_exists($this, '_'.$property)) {
                $this->{'_'.$property} = $data;
            }
        }

        if (!$this->id) {
            $this->id = 'widget_'.static::$_counter;
        }

        if (isset($this->_html_options['id'])) {
            $this->id = $this->_html_options['id'];
        } else {
            $this->_html_options['id'] = $this->id;
        }
    }

    /**
     * @param string $template
     *
     * @throws Exception
     *
     * @return string
     */
    protected function renderFile($template)
    {
        ob_start();

        $app_template_file = CORDILLERA_APP_DIR.$template.'.php';
        $cordillera_template_file = CORDILLERA_DIR.$template.'.php';

        if (is_file($app_template_file)) {
            include $app_template_file;
        } elseif (!is_file($app_template_file) && is_file($cordillera_template_file)) {
            include $cordillera_template_file;
        } else {
            throw new Exception(
                translate('The widget view %s not found', [$this->_template]),
                500,
                Exception::VIEW
            );
        }

        $output = ob_get_contents();

        ob_end_clean();

        return $output;
    }

    /**
     * @param array $attributes
     *
     * @return string
     */
    protected function bind(array $attributes = [])
    {
        return implode(' ', array_map(function ($attribute, $value) {
            return "{$attribute}=\"{$value}\"";
        }, array_keys($attributes), $attributes));
    }

    /**
     * @return string
     */
    protected function renderContent()
    {
        $return = '';
        if ($this->_renderer) {
            $return = call_user_func_array($this->_renderer, [$this]);
        }

        return $return;
    }

    /**
     * @return string
     */
    public function render()
    {
        return $this->renderFile($this->_template);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}
