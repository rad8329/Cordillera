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

namespace modules\examples\routes;

use cordillera\middlewares\Controller;
use cordillera\middlewares\Layout;
use cordillera\middlewares\View;

/* @var Controller $this */

$this->get(function () {

    /* @var Controller $this */

    $view = new View('modules/site/views/welcome', new Layout('main'));

    $this->setResponse($view);
});
