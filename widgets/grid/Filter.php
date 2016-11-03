<?php

namespace cordillera\widgets\grid;

use cordillera\widgets\Widget;

class Filter extends Widget
{
    protected $_html = '<input {attributes}/>';

    /**
     * @return string
     */
    public function render()
    {
        return str_replace(
            ['{attributes}', '{data}'],
            [$this->bind($this->_html_options), $this->renderContent()],
            $this->_html
        );
    }
}
