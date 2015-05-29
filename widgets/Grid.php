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

use cordillera\middlewares\db\ActiveDataProvider;
use cordillera\middlewares\Exception;
use cordillera\widgets\grid\Filter;
use cordillera\widgets\grid\Column;
use cordillera\base\interfaces\DataProvider as DataProviderInterface;

class Grid extends Widget
{
    /**
     * @var ActiveDataProvider
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
        if (!isset($config['data_provider'])) {
            throw new Exception(translate('{data_provider} must be a DatapPovider objetc'), 500, Exception::BADARGUMENTS);
        } elseif (isset($config['data_provider']) && !($config['data_provider'] instanceof DataProviderInterface)) {
            throw new Exception(translate('{data_provider} must be a DatapPovider objetc'), 500, Exception::BADARGUMENTS);
        }

        if (!isset($config['columns'])) {
            throw new Exception(translate('{columns} must be an array type Column[]'), 500, Exception::BADARGUMENTS);
        }

        parent::setup($config);

        $this->setupColumns();
    }

    protected function setupColumns()
    {
        if ($this->_data_provider->isActiveRecord()) {
            foreach ($this->_columns as $key => $column) {
                if (is_string($column) && property_exists($this->_data_provider->data_source, $column)) {
                    if ((new \ReflectionProperty($this->_data_provider->data_source, $column))->isPublic()) {
                        $this->_columns[$key] = new Column([
                            'attribute' => $column,
                            'header' => ucfirst($column),
                            'value' => $column,
                            'filter' => new Filter([
                                'html_options' => [
                                    'name' => (new \ReflectionClass($this->_data_provider->data_source))->getShortName()."[{$column}]",
                                    'type' => 'text',
                                    'class' => 'form-control',
                                    'value' => (isset($this->_data_provider->request_params[$column]) ?
                                        $this->_data_provider->request_params[$column]
                                        : ''
                                    ),
                                    'placeholder' => translate('filter by %s', [$column]),
                                ],
                            ]),
                        ]);
                    }
                } else {
                    unset($this->_columns[$key]);
                }
            }
        }
    }

    /**
     * @return string
     */
    public function renderHeaders()
    {
        $html = '<thead><tr>';

        foreach ($this->_columns as $column) {
            $html .= $column->render('header');
        }

        $html .= '</tr></thead>';

        return $html;
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
        $html = '<tbody>';
        foreach ($this->_data_provider->getData() as $record) {
            $html .= '<tr>';
            foreach ($this->_columns as $column) {
                $column->bindRecord($record);
                $html .= $column->render();
            }
            $html .= '</tr>';
        }

        $html .= '</tbody>';

        return $html;
    }

    /**
     * @return string
     */
    protected function renderContent()
    {
        if ($this->_renderer) {
            return call_user_func_array($this->_renderer, [$this]);
        } else {
            return '<table class="table table-hover">'.$this->renderHeaders().$this->renderBody().'</table>';
        }
    }
}
