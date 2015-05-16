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

namespace cordillera\widgets\grid;

use cordillera\widgets\Widget;

class Column extends Widget
{
    /**
     * @return string
     */
    protected $_attribute;

    /**
     * @return string
     */
    protected $_header;

    /**
     * @var mixed
     */
    protected $_value;

    /**
     * @var mixed
     */
    protected $_record;

    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * @return string
     */
    protected function renderHeader()
    {
    }

    /**
     * @return string
     */
    protected function renderFilter()
    {
        $return = '';
        if (isset($this->_filter)) {
            $return = $this->_filter->render();
        }

        return $return;
    }

    /**
     * @return string
     */
    protected function renderContent()
    {
    }

    /**
     * @return string
     */
    protected function renderSummary()
    {
    }

    /**
     * @param string $part
     *
     * @return string
     */
    public function render($part = 'content')
    {
        $return = '';
        switch ($part) {
            case 'content':
                $return = $this->renderContent();
                break;
            case 'filter':
                $return = $this->renderFilter();
                break;
            case 'summary':
                $return = $this->renderSummary();
                break;
            case 'header':
                $return = $this->renderHeader();
                break;
        }

        return $return;
    }
}
