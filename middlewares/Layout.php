<?php

namespace cordillera\middlewares;

use cordillera\base\interfaces\Display;
use cordillera\base\interfaces\Layout as LayoutInterface;

class Layout implements Display, LayoutInterface
{
    /**
     * Scope for publish the registered files on head.
     */
    const HEAD_SCOPE = 'head';

    /**
     * Scope for publish the registered files on end body.
     */
    const END_SCOPE = 'end';
    /**
     * @var string
     */
    protected $_template = '';

    /**
     * @var string
     */
    protected $_output = '';

    /**
     * @var array
     */
    public $properties = [];

    /**
     * @var array
     */
    public $assets = [
        'js' => [
            self::HEAD_SCOPE => [],
            self::END_SCOPE => [],
        ],
        'css' => [],
    ];

    /**
     * @var Request
     */
    public $request;

    /**
     * @param string $template
     */
    public function __construct($template = '')
    {
        $this->_template = $template;
    }

    /**
     * @param $property
     * @param $default
     *
     * @return mixed
     */
    public function getProperty($property, $default = '')
    {
        return isset($this->properties[$property]) ? $this->properties[$property] : $default;
    }

    /**
     * @param string $content
     *
     * @throws Exception
     *
     * @return string
     */
    public function render($content = '')
    {
        $this->_template = empty($this->_template) ? 'blank' : $this->_template;

        ob_start();

        $app_layout_file = CORDILLERA_APP_DIR.'layouts'.DS.$this->_template.'.php';
        $cordillera_layout_file = CORDILLERA_DIR.'layouts'.DS.$this->_template.'.php';

        if (is_file($app_layout_file)) {
            require_once $app_layout_file;
        } elseif (!is_file($app_layout_file) && is_file($cordillera_layout_file)) {
            require_once $cordillera_layout_file;
        } else {
            throw new Exception(
                translate('The layout %s not found', [$this->_template]),
                500,
                Exception::LAYOUT
            );
        }

        $this->_output = ob_get_contents();

        ob_end_clean();

        return $this->_output;
    }

    /**
     * @param string $file  valid js file name
     * @param string $scope valid scope name
     */
    public function registerJsFile($file, $scope = self::HEAD_SCOPE)
    {
        $file_id = md5($file);
        if ($scope == self::HEAD_SCOPE) {
            unset($this->assets['js'][self::END_SCOPE][$file_id]);
        } else {
            unset($this->assets['js'][self::HEAD_SCOPE][$file_id]);
        }

        $this->assets['js'][$scope][$file_id] = $file;
    }

    /**
     * @param string $file A valid css file name
     */
    public function registerCssFile($file)
    {
        $this->assets['css'][md5($file)] = $file;
    }

    /**
     * @param string $scope valid scope name
     *
     * @return string Content (HTML) of js and css tags
     */
    public function publishRegisteredFiles($scope = self::HEAD_SCOPE)
    {
        $tags = '';

        foreach ($this->assets['js'][$scope] as $js) {
            if ($js) {
                $tags .= "\n<script src=\"$js\" type=\"text/javascript\"></script>";
            }
        }

        if ($scope == self::HEAD_SCOPE) {
            foreach ($this->assets['css'] as $css) {
                if ($css) {
                    $tags .= "\n<link rel=\"stylesheet\" type=\"text/css\" href=\"$css\">";
                }
            }
        }

        return $tags;
    }
}
