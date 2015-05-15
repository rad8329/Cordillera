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

namespace cordillera\widgets;

use cordillera\base\Application;
use cordillera\middlewares\db\DataProvider;
use cordillera\middlewares\Exception;
use cordillera\widgets\grid\Column;

class Grid extends Widget
{
    /**
     * @var DataProvider
     */
    protected $_data_provider;

    /**
     * @var Column[]
     */
    protected $_columns = [];

    /**
     * @param array $config
     *
     * @throws Exception
     */
    protected function setup(array $config = [])
    {
        parent::setup($config);
        if (!isset($config['data_provider'])) {
            throw new Exception(Application::getLang()->translate('{data_provider} must be a DatapPovider objetc'), 500, Exception::BADARGUMENTS);
        } elseif (isset($config['data_provider']) && !($config['data_provider'] instanceof DataProvider)) {
            throw new Exception(Application::getLang()->translate('{data_provider} must be a DatapPovider objetc'), 500, Exception::BADARGUMENTS);
        }
    }

    /*
    private function setupFilters()
    {
        foreach ($this->_filters as &$filter) {
            $filter->html = str_replace(
                [
                    '{name}',
                    '{value}',
                    '{id}'
                ],
                [
                    $this->_request_context . "[{$filter->request_param}]",
                    isset($this->_request_params[$filter->request_param]) ? $this->_request_params[$filter->request_param] : '',
                    strtolower($this->_request_context . "_" . $filter->request_param . "_id")
                ], $filter->html);
        }
    }
    */

    /**
     * @return string
     */
    public function renderHeaders()
    {
        foreach($this->_columns as $colum){

        }
        return '<div>headers</div>';
    }

    /**
     * @return string
     */
    public function renderPagination()
    {
        return '<div>pagination</div>';
    }

    /**
     * @return string
     */
    public function renderSummaries()
    {
        return '<div>summaries</div>';
    }

    /**
     * @return string
     */
    public function renderBody()
    {
        return '<div>body</div>';
    }

    /**
     * @return string
     */
    protected function renderContent()
    {
        if ($this->_renderer) {
            return call_user_func_array($this->_renderer, [$this]);
        } else {
            return $this->renderHeaders() . $this->renderBody() . $this->renderSummaries() . $this->renderPagination();
        }
    }
}
