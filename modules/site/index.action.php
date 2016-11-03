<?php

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
