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

class Response
{
    public function setSecurityHeaders()
    {
        $this->setHeader('x-content-type-options', 'nosniff');
        $this->setHeader('x-frame-options', 'sameorigin');
        $this->setHeader('x-permitted-cross-domain-policies', 'master-only');
        $this->setHeader('x-xss-protection', '1; mode=block');
        $this->setHeader('X-Powered-By', 'PHP');
    }

    /**
     * @param string      $header
     * @param null|string $value
     * @param bool        $replace
     * @param null|int    $http_response_code
     */
    public function setHeader($header, $value = null, $replace = true, $http_response_code = null)
    {
        header("$header".($value ? ": $value" : ''), $replace, $http_response_code);
    }

    /**
     * @param int  $statusCode HTTP status code
     * @param bool $exit
     */
    public function headerStatus($statusCode, $exit = false)
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
            $status_string = $statusCode.' '.$status_codes[$statusCode];
            $this->setHeader($_SERVER['SERVER_PROTOCOL'].' '.$status_string, null, true, $statusCode);
        }

        if ($exit) {
            exit;
        }
    }

    /**
     * @param mixed $content
     */
    public function json($content)
    {
        $this->setHeader('Content-Type', 'application/json');
        echo json_encode($content, JSON_PRETTY_PRINT);
    }

    /**
     * @param string $content
     */
    public function raw($content)
    {
        if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)) {
            $this->setHeader('X-UA-Compatible', 'IE=edge,chrome=1');
        }

        echo $content;
    }
}
