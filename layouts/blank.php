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

use cordillera\middlewares\Layout;

/* @var string $content Content of buffer */
/* @var Layout $this */

echo $this->publishRegisteredFiles();
echo $content;
echo $this->publishRegisteredFiles(Layout::END_SCOPE);
