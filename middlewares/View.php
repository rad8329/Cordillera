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

namespace cordillera\middlewares;

use cordillera\base\Cordillera;
use cordillera\base\interfaces\Display;

class View implements Display
{
    /**
     * @var string
     */
    protected $_template = '';

    /**
     * @var string
     */
    protected $_output = '';

    /**
     * @var null|Layout A layout valid name
     */
    public $layout = null;

    /**
     * @var array
     */
    public $data = [];

    /**
     * @param string $template A file valid name
     * @param Layout $layout
     * @param array  $data
     */
    public function __construct($template, Layout $layout = null, array $data = [])
    {
        $this->_template = $template;
        $this->data = $data;
        $this->layout = $layout;
    }

    /**
     * @throws Exception
     *
     * @return string
     */
    public function render()
    {
        foreach ($this->data as $key => $var_data) {
            $$key = $var_data;
        }

        ob_start();

        $app_template_file = CORDILLERA_APP_DIR.$this->_template.'.php';
        $cordillera_template_file = CORDILLERA_DIR.$this->_template.'.php';

        if (is_file($app_template_file)) {
            require_once $app_template_file;
        } elseif (!is_file($app_template_file) && is_file($cordillera_template_file)) {
            require_once $cordillera_template_file;
        } else {
            throw new Exception(
                Cordillera::app()->lang->translate('The view %s not found', [$this->_template]),
                500,
                Exception::VIEW
            );
        }

        $this->_output = ob_get_contents();

        ob_end_clean();

        if ($this->layout && !Cordillera::app()->request->isAjax() && !Cordillera::app()->controller->is_rest && Cordillera::app()->controller->response_type != 'json') {
            $this->_output = $this->layout->render($this->_output);
        }

        return $this->_output;
    }
}
