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
use cordillera\base\interfaces\Filter as FilterInterface;
use cordillera\middlewares\Exception;

class Filter implements FilterInterface
{
    /**
     * @var FilterInterface[]
     */
    protected $_filters = [];

    /**
     * @throws Exception
     */
    public function assertJsonContentType()
    {
        if (
            !isset($_SERVER['CONTENT_TYPE']) ||
            (isset($_SERVER['CONTENT_TYPE']) && !preg_match('/^application\/json/', $_SERVER['CONTENT_TYPE']))
        ) {
            throw new Exception(Cordillera::app()->lang->translate('Bad request'), 400, Exception::BADREQUEST);
        }
    }

    /**
     * @throws Exception
     */
    public function assertCsrfToken()
    {
        if (Cordillera::app()->config->get('request.csrf') && Cordillera::app()->request->isPost() && !Cordillera::app()->controller->is_rest) {
            // If the CSRF token is enabled, and is post method request
            $request = Cordillera::app()->request;
            $payload = Cordillera::app()->request->payload(Cordillera::app()->request->csrf_id);
            $post = Cordillera::app()->request->post(Cordillera::app()->request->csrf_id);

            if (
                // POST data
                (empty($payload) && $post != $request->csrf_value) ||
                // Payload data
                (!empty($payload) && $payload != $request->csrf_value)
            ) {
                throw new Exception(Cordillera::app()->lang->translate('Bad request'), 400, Exception::BADREQUEST);
            }
        }
    }

    /**
     * @throws Exception
     */
    public function assertAjax()
    {
        if (!Cordillera::app()->request->isAjax()) {
            throw new Exception(Cordillera::app()->lang->translate('Bad request'), 400, Exception::BADREQUEST);
        }
    }

    /**
     * @param FilterInterface $filter
     */
    public function add(FilterInterface $filter)
    {
        $this->_filters[] = $filter;
    }

    public function execute()
    {
        foreach ($this->_filters as $filter) {
            $filter->execute();
        }
    }
}
