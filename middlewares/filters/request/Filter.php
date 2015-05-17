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
            throw new Exception(translate('Bad request'), 400, Exception::BADREQUEST);
        }
    }

    /**
     * @throws Exception
     */
    public function assertCsrfToken()
    {
        if (app()->config->get('request.csrf') && app()->request->isPost() && !app()->controller->is_rest) {
            // If the CSRF token is enabled, and is post method request
            $request = app()->request;
            $payload = app()->request->payload(app()->request->csrf_id);
            $post = app()->request->post(app()->request->csrf_id);

            if (
                // POST data
                (empty($payload) && $post != $request->csrf_value) ||
                // Payload data
                (!empty($payload) && $payload != $request->csrf_value)
            ) {
                throw new Exception(translate('Bad request'), 400, Exception::BADREQUEST);
            }
        }
    }

    /**
     * @throws Exception
     */
    public function assertAjax()
    {
        if (!app()->request->isAjax()) {
            throw new Exception(translate('Bad request'), 400, Exception::BADREQUEST);
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
