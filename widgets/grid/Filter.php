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
