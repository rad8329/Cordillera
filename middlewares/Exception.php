<?php

namespace cordillera\middlewares;

use cordillera\base\interfaces\Exception as ExceptionInterface;

class Exception extends \Exception implements ExceptionInterface
{
    const UNKNOWN = -1;
    const DBCONNECTION = 1;
    const NOTFOUND = 2;
    const VIEW = 3;
    const LAYOUT = 4;
    const BADREQUEST = 5;
    const BADARGUMENTS = 6;
    const DBSTATEMENT = 7;
    const SQLPARAMS = 8;
    const ROUTING = 9;
    const QUERY = 10;
    const ERROR = 11;
    const FORBIDDEN = 12;
    const FILESYSTEM = 13;

    /**
     * @var array
     */
    public static $types = [
        self::UNKNOWN => 'UnknownException',
        self::DBCONNECTION => 'DbConnectionException',
        self::NOTFOUND => 'NotFoundException',
        self::VIEW => 'ViewException',
        self::LAYOUT => 'LayoutException',
        self::BADREQUEST => 'HttpException',
        self::BADARGUMENTS => 'ArgumentsException',
        self::DBSTATEMENT => 'DbStatementException',
        self::SQLPARAMS => 'SqlParamsException',
        self::QUERY => 'QueryException',
        self::ROUTING => 'RoutingException',
        self::ERROR => 'ErrorException',
        self::FORBIDDEN => 'ForbiddenException',
        self::FILESYSTEM => 'FileSystemException',
    ];

    /**
     * @param string $message
     * @param int    $status_code
     * @param int    $code
     * @param mixed  $previous
     */
    public function __construct($message = '', $status_code = 500, $code = -1, $previous = null)
    {
        parent::__construct($message, $code, $previous);

        app()->response->headerStatus($status_code, false);
    }

    /**
     * @return array
     */
    public function getAllTraces()
    {
        $trace = [];

        if ($this->getPrevious()) {
            $trace[] = $this->getPrevious()->getFile().' line '.$this->getPrevious()->getLine();
        }
        $trace[] = $this->getFile().' line '.$this->getLine();
        foreach ($this->getTrace() as $_trace) {
            if (isset($_trace['file'])) {
                $trace[] = $_trace['file'].' line '.$_trace['line'];
            }
        }

        return $trace;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        $html = '';

        if (CORDILLERA_DEBUG) {
            $html .= "<strong>{$this->getMessage()}</strong>";
            $html .= '<div class="trace">';
            $html .= implode("\n", array_map(function ($trace) {
                return "<div>{$trace}</div>";
            }, $this->getAllTraces())).'</div>';
        } else {
            $html = $this->getMessage();
        }

        if (app()->config->get('exception.show_log_id') && app()->logger->last_log_id) {
            $html .= '<div class="clearfix log-info"><div class="pull-right">log_id: <strong>'.app()->logger->last_log_id.'</strong></div></div>';
        }

        return $html;
    }
}
