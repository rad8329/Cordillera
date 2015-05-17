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

namespace cordillera\middlewares\filters\request;

use cordillera\base\Cordillera;
use cordillera\base\interfaces\Filter;

class Cors implements Filter
{
    /**
     * @var array
     */
    protected $_cors = [
        'Origin' => ['*'],
        'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
        'Access-Control-Request-Headers' => ['*'],
        'Access-Control-Allow-Credentials' => null,
        'Access-Control-Max-Age' => 86400,
        'Access-Control-Expose-Headers' => [],
    ];

    /**
     * @param array $cors
     */
    public function __construct(array $cors = [])
    {
        $this->_cors = array_merge($this->_cors, $cors);
    }

    public function execute()
    {
        $requestCorsHeaders = $this->extractHeaders();
        $responseCorsHeaders = $this->prepareHeaders($requestCorsHeaders);
        $this->addCorsHeaders($responseCorsHeaders);

        if (isset($requestCorsHeaders['Access-Control-Request-Headers'])) {
            exit;
        }
    }

    /**
     * Extract CORS headers from the request.
     *
     * @return array CORS headers to handle
     */
    public function extractHeaders()
    {
        $headers = [];
        $requestHeaders = array_keys($this->_cors);
        foreach ($requestHeaders as $headerField) {
            $serverField = Cordillera::app()->request->headerizeToPhp($headerField);
            $headerData = isset($_SERVER[$serverField]) ? $_SERVER[$serverField] : null;
            if ($headerData !== null) {
                $headers[$headerField] = $headerData;
            }
        }

        return $headers;
    }

    /**
     * Adds the CORS headers to the response.
     *
     * @param array CORS headers which have been computed
     */
    public function addCorsHeaders($headers)
    {
        if (empty($headers) === false) {
            foreach ($headers as $header => $value) {
                Cordillera::app()->response->setHeader($header, $value);
            }
        }
    }

    /**
     * For each CORS headers create the specific response.
     *
     * @param array $requestHeaders CORS headers we have detected
     *
     * @return array CORS headers ready to be sent
     */
    public function prepareHeaders($requestHeaders)
    {
        $responseHeaders = [];

        if (isset($requestHeaders['Origin'], $this->_cors['Origin'])) {
            if (in_array('*', $this->_cors['Origin']) || in_array($requestHeaders['Origin'], $this->_cors['Origin'])) {
                $responseHeaders['Access-Control-Allow-Origin'] = $requestHeaders['Origin'];
            }
        }

        $this->prepareAllowHeaders('Headers', $requestHeaders, $responseHeaders);

        if (isset($requestHeaders['Access-Control-Request-Method'])) {
            $responseHeaders['Access-Control-Allow-Methods'] = implode(', ', $this->_cors['Access-Control-Request-Method']);
        }

        if (isset($this->_cors['Access-Control-Allow-Credentials'])) {
            $responseHeaders['Access-Control-Allow-Credentials'] = $this->_cors['Access-Control-Allow-Credentials'] ? 'true' : 'false';
        }

        if (isset($this->_cors['Access-Control-Max-Age']) && Cordillera::app()->request->isOptions()) {
            $responseHeaders['Access-Control-Max-Age'] = $this->_cors['Access-Control-Max-Age'];
        }

        if (isset($this->_cors['Access-Control-Expose-Headers'])) {
            $responseHeaders['Access-Control-Expose-Headers'] = implode(', ', $this->_cors['Access-Control-Expose-Headers']);
        }

        return $responseHeaders;
    }

    /**
     * Handle classic CORS request to avoid duplicate code.
     *
     * @param string $type            the kind of headers we would handle
     * @param array  $requestHeaders  CORS headers request by client
     * @param array  $responseHeaders CORS response headers sent to the client
     */
    protected function prepareAllowHeaders($type, $requestHeaders, &$responseHeaders)
    {
        $requestHeaderField = 'Access-Control-Request-'.$type;
        $responseHeaderField = 'Access-Control-Allow-'.$type;
        if (!isset($requestHeaders[$requestHeaderField], $this->_cors[$requestHeaderField])) {
            return;
        }
        if (isset($this->_cors[$requestHeaderField]) && in_array('*', $this->_cors[$requestHeaderField])) {
            $responseHeaders[$responseHeaderField] = Cordillera::app()->request->headerize($requestHeaders[$requestHeaderField]);
        } else {
            $requestedData = preg_split('/[\\s,]+/', $requestHeaders[$requestHeaderField], -1, PREG_SPLIT_NO_EMPTY);
            $acceptedData = array_uintersect($requestedData, $this->_cors[$requestHeaderField], 'strcasecmp');
            if (!empty($acceptedData)) {
                $responseHeaders[$responseHeaderField] = implode(', ', $acceptedData);
            }
        }
    }
}
