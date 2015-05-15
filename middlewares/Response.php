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

class Response
{
    public static function setSecurityHeaders()
    {
        self::setHeader('x-content-type-options', 'nosniff');
        self::setHeader('x-frame-options', 'sameorigin');
        self::setHeader('x-permitted-cross-domain-policies', 'master-only');
        self::setHeader('x-xss-protection', '1; mode=block');
        self::setHeader('X-Powered-By', 'PHP');
    }

    /**
     * @param string $header
     * @param null|string $value
     * @param bool $replace
     * @param null|int $http_response_code
     */
    public static function setHeader($header, $value = null, $replace = true, $http_response_code = null)
    {
        header("$header" . ($value ? ": $value" : ""), $replace, $http_response_code);
    }

    /**
     * @param int $statusCode HTTP status code
     * @param bool $exit
     */
    public static function headerStatus($statusCode, $exit = false)
    {
        static $status_codes = null;

        if ($status_codes === null) {
            $status_codes = [
                100 => 'Continue',
                101 => 'Switching Protocols',
                102 => 'Processing',
                200 => 'OK',
                201 => 'Created',
                202 => 'Accepted',
                203 => 'Non-Authoritative Information',
                204 => 'No Content',
                205 => 'Reset Content',
                206 => 'Partial Content',
                207 => 'Multi-Status',
                300 => 'Multiple Choices',
                301 => 'Moved Permanently',
                302 => 'Found',
                303 => 'See Other',
                304 => 'Not Modified',
                305 => 'Use Proxy',
                307 => 'Temporary Redirect',
                400 => 'Bad Request',
                401 => 'Unauthorized',
                402 => 'Payment Required',
                403 => 'Forbidden',
                404 => 'Not Found',
                405 => 'Method Not Allowed',
                406 => 'Not Acceptable',
                407 => 'Proxy Authentication Required',
                408 => 'Request Timeout',
                409 => 'Conflict',
                410 => 'Gone',
                411 => 'Length Required',
                412 => 'Precondition Failed',
                413 => 'Request Entity Too Large',
                414 => 'Request-URI Too Long',
                415 => 'Unsupported Media Type',
                416 => 'Requested Range Not Satisfiable',
                417 => 'Expectation Failed',
                422 => 'Unprocessable Entity',
                423 => 'Locked',
                424 => 'Failed Dependency',
                426 => 'Upgrade Required',
                500 => 'Internal Server Error',
                501 => 'Not Implemented',
                502 => 'Bad Gateway',
                503 => 'Service Unavailable',
                504 => 'Gateway Timeout',
                505 => 'HTTP Version Not Supported',
                506 => 'Variant Also Negotiates',
                507 => 'Insufficient Storage',
                509 => 'Bandwidth Limit Exceeded',
                510 => 'Not Extended',
            ];
        }

        if (isset($status_codes[$statusCode])) {
            $status_string = $statusCode . ' ' . $status_codes[$statusCode];
            self::setHeader($_SERVER['SERVER_PROTOCOL'] . ' ' . $status_string, null, true, $statusCode);
        }

        if ($exit) {
            exit;
        }
    }

    /**
     * @param mixed $content
     */
    public static function json($content)
    {
        self::setHeader('Content-Type', 'application/json');
        echo json_encode($content, JSON_PRETTY_PRINT);
    }

    /**
     * @param string $content
     */
    public static function raw($content)
    {
        if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)) {
            self::setHeader('X-UA-Compatible', 'IE=edge,chrome=1');
        }

        echo $content;
    }

    /**
     * @param Exception $exception
     */
    public static function exception(Exception $exception)
    {
        if (Request::isAjax() || (Application::getController()->type == 'json' || Application::getController()->rest)) {
            $response = ['error' => true, 'message' => $exception->getMessage()];

            if (CORDILLERA_DEBUG) {
                $response['trace'] = $exception->getAllTraces();
            }

            if (Application::getConfig()->get('exception.show_log_id') && Application::getLogger()->last_log_id) {
                $response['log_id'] = Application::getLogger()->last_log_id;
            }

            self::json($response);
        } else {
            $layout = new Layout('error');

            if (CORDILLERA_DEBUG) {
                $layout->properties['title'] = Exception::$types[$exception->getCode()];
            }
            if (ob_get_length()) {
                ob_end_clean();
            }

            echo $layout->render($exception->toHtml());
        }
        exit;
    }
}
