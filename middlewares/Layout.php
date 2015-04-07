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

use cordillera\base\Application;
use cordillera\base\interfaces\Display;
use cordillera\base\interfaces\Layout as LayoutInterface;

class Layout implements Display, LayoutInterface
{
    /**
     * @var string
     */
    protected $_template = "";

    /**
     * @var string
     */
    protected $_output = "";

    /**
     * @var array
     */
    public $properties = [];

    /**
     * @var array
     */
    public $assets = [
        'js' => [],
        'css' => []
    ];

    /**
     * @var Request
     */
    public $request;

    /**
     * @param string $template
     */
    public function __construct($template = "")
    {
        $this->_template = $template;
    }

    /**
     * @param $property
     * @param $default
     * @return mixed
     */
    public function getProperty($property, $default = "")
    {
        return isset($this->properties[$property]) ? $this->properties[$property] : $default;
    }

    /**
     * @param string $content
     * @throws Exception
     * @return string
     */
    public function  render($content = "")
    {
        $this->_template = empty($this->_template) ? "blank" : $this->_template;

        ob_start();

        $app_layout_file = CORDILLERA_APP_DIR . 'layouts' . DS . $this->_template . '.php';
        $cordillera_layout_file = CORDILLERA_DIR . 'layouts' . DS . $this->_template . '.php';

        if (is_file($app_layout_file)) {
            require_once $app_layout_file;
        } elseif (!is_file($app_layout_file) && is_file($cordillera_layout_file)) {
            require_once $cordillera_layout_file;
        } else {
            throw new Exception(
                Application::getLang()->translate('The layout %s not found', [$this->_template]),
                500,
                Exception::LAYOUT
            );
        }

        $this->_output = ob_get_contents();

        ob_end_clean();

        return $this->_output;
    }

    /**
     * @param string $file A valid js file name
     */
    public function registerJsFile($file)
    {
        $this->assets['js'][md5($file)] = $file;
    }

    /**
     * @param string $file A valid css file name
     */
    public function registerCssFile($file)
    {
        $this->assets['css'][md5($file)] = $file;
    }

    /**
     * @return string Content (HTML) of js and css tags
     */
    public function publishRegisteredFiles()
    {
        $tags = "";

        foreach ($this->assets["js"] as $js) {
            if ($js) {
                $tags .= "\n<script src=\"$js\" type=\"text/javascript\"></script>";
            }
        }

        foreach ($this->assets["css"] as $css) {
            if ($css) {
                $tags .= "\n<link rel=\"stylesheet\" type=\"text/css\" href=\"$css\">";
            }
        }

        return $tags;
    }
}