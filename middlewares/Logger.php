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

use cordillera\helpers\Crypt;
use DateTime;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class Logger extends AbstractLogger
{
    /**
     * @var bool|string
     */
    protected $_filename = false;

    /**
     * @var string
     */
    protected $_full_filename = '';

    /**
     * @var string
     */
    protected $_date_format = 'Y-m-d G:i:s.u';

    /**
     * @var bool
     */
    protected $_flush_frequency = false;

    /**
     * @var string
     */
    protected $_prefix = 'log_';

    /**
     * @var string
     */
    protected $_path;

    /**
     * @var int
     */
    protected $_level = LogLevel::DEBUG;

    /**
     * @var int
     */
    protected $_line_count = 0;

    /**
     * @var array
     */
    protected $_levels = [
        LogLevel::EMERGENCY => 0,
        LogLevel::ALERT => 1,
        LogLevel::CRITICAL => 2,
        LogLevel::ERROR => 3,
        LogLevel::WARNING => 4,
        LogLevel::NOTICE => 5,
        LogLevel::INFO => 6,
        LogLevel::DEBUG => 7,
    ];

    /**
     * @var resource
     */
    protected $_file_handler;

    /**
     * @var string
     */
    protected $_last_line = '';

    /**
     * @var int
     */
    protected $_permissions = 0777;

    /**
     * @var string
     */
    public $last_log_id = '';

    /**
     * @param array $options
     *
     * @throws Exception
     */
    public function __construct(array $options = [])
    {
        $_this = $this;
        if (!isset($options['level'])) {
            $options['level'] = CORDILLERA_DEBUG ? LogLevel::DEBUG : LogLevel::WARNING;
        }

        if (!isset($options['path'])) {
            $options['path'] = CORDILLERA_APP_DIR.'logs';
        }

        foreach ($options as $option => $value) {
            if (property_exists(get_class($this), '_'.$option)) {
                $_this->{'_'.$option} = $value;
            } else {
                throw new Exception("The property [$option] does not exists", 500, Exception::BADARGUMENTS);
            }
        }

        $this->init();
    }

    public function __destruct()
    {
        if ($this->_file_handler) {
            fclose($this->_file_handler);
        }
    }

    /**
     * @throws Exception
     */
    private function init()
    {
        $this->_path = rtrim($this->_path, DS);

        if (!file_exists($this->_path)) {
            mkdir($this->_path, $this->_permissions, true);
        }

        if ($this->_filename) {
            $this->_full_filename = $this->_path.DS.$this->_filename.'.data';
        } else {
            $this->_full_filename = $this->_path.DS.$this->_prefix.date('Y_m_d').'.data';
        }

        if (file_exists($this->_full_filename) && !is_writable($this->_full_filename)) {
            throw new Exception('The file could not be written', 500, Exception::FILESYSTEM);
        }

        $this->_file_handler = fopen($this->_full_filename, 'a');

        if (!$this->_file_handler) {
            throw new Exception('The file could not be opened', 500, Exception::FILESYSTEM);
        }
    }

    /**
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return bool
     */
    public function log($level, $message, array $context = array())
    {
        $log_id = '';
        if ($this->_levels[$this->_level] < $this->_levels[$level]) {
            return false;
        }

        if ($this->_levels[$level] <= $this->_levels[LogLevel::ERROR]) {
            $this->last_log_id = $this->generateId();
            $log_id = "{{$this->last_log_id}} ";
        }

        if (!empty($context)) {
            $message .= PHP_EOL.$this->indent($this->dumpContext($context));
        }

        $message = "[{$this->getTimestamp()}] [".strtoupper($level)."] {$log_id}{$message}".PHP_EOL;

        $this->write($message);

        return true;
    }

    /**
     * @param string $message
     *
     * @throws Exception
     */
    public function write($message)
    {
        if (null !== $this->_file_handler) {
            if (fwrite($this->_file_handler, $message) === false) {
                throw new Exception('The file could not be written', 500, Exception::FILESYSTEM);
            } else {
                $this->_last_line = trim($message);
                $this->_line_count++;

                if ($this->_flush_frequency && $this->_line_count % $this->_flush_frequency === 0) {
                    fflush($this->_file_handler);
                }
            }
        }
    }

    /**
     * @param string $string
     * @param string $indent
     *
     * @return string
     */
    protected function indent($string, $indent = '    ')
    {
        return $indent.str_replace("\n", "\n".$indent, $string);
    }

    /**
     * @return string
     */
    protected function getTimestamp()
    {
        $originalTime = microtime(true);
        $micro = sprintf('%06d', ($originalTime - floor($originalTime)) * 1000000);
        $date = new DateTime(date('Y-m-d H:i:s.'.$micro, $originalTime));

        return $date->format($this->_date_format);
    }

    /**
     * @param array $context
     *
     * @return string
     */
    protected function dumpContext($context)
    {
        $export = '';
        foreach ($context as $key => $value) {
            $export .= "{$key}: ";
            $export .= preg_replace(array(
                '/=>\s+([a-zA-Z])/im',
                '/array\(\s+\)/im',
                '/^  |\G  /m',
            ), array(
                '=> $1',
                'array()',
                '    ',
            ), str_replace('array (', 'array(', var_export($value, true)));
            $export .= PHP_EOL;
        }

        return str_replace(array('\\\\', '\\\''), array('\\', '\''), rtrim($export));
    }

    /**
     * @return string
     */
    public function generateId()
    {
        return implode('-', str_split(strtoupper(Crypt::create(12)), 4));
    }
}
