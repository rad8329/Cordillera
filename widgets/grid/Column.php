<?php

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
        return '<th>'.$this->renderCaption().$this->renderFilter().'</th>';
    }

    /**
     * @return string
     */
    protected function renderFilter()
    {
        $return = '<div class="filter">';
        if (isset($this->_filter)) {
            $return .= $this->_filter->render();
        }
        $return .= '</div>';

        return $return;
    }

    protected function renderCaption()
    {
        return '<div class="caption">'.$this->_header.'</div>';
    }

    /**
     * @return string
     */
    protected function renderContent()
    {
        $value = '';

        if (is_callable($this->_value)) {
            $value = call_user_func_array($this->_value, [$this->_record]);
        } else {
            if (property_exists($this->_record, $this->_value)) {
                $value = $this->_record->{$this->_value};
            }
        }

        return "<td>{$value}</td>";
    }

    /**
     * @return string
     */
    protected function renderSummary()
    {
        return;
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

    public function bindRecord($record)
    {
        $this->_record = $record;
    }
}
