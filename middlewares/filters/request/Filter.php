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

use cordillera\base\Application;
use cordillera\base\interfaces\Filter as FilterInterface;
use cordillera\middlewares\Exception;
use cordillera\middlewares\Request;

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
            throw new Exception(Application::getLang()->translate('Bad request'), 400, Exception::BADREQUEST);
        }
    }

    /**
     * @throws Exception
     */
    public function assertCsrfToken()
    {
        if (Application::getConfig()->get('request.csrf') && Request::isPost() && !Application::getController()->rest) {
            // If the CSRF token is enabled, and is post method request
            $request = Application::getRequest();
            $payload = Request::payload(Application::getRequest()->csrf_id);
            $post = Request::post(Application::getRequest()->csrf_id);

            if (
                // POST data
                (empty($payload) && $post != $request->csrf_value) ||
                // Payload data
                (!empty($payload) && $payload != $request->csrf_value)
            ) {
                throw new Exception(Application::getLang()->translate('Bad request'), 400, Exception::BADREQUEST);
            }
        }
    }

    /**
     * @throws Exception
     */
    public function assertAjax()
    {
        if (!Request::isAjax()) {
            throw new Exception(Application::getLang()->translate('Bad request'), 400, Exception::BADREQUEST);
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
