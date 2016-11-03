<?php

use cordillera\middlewares\Layout;

/* @var string $content Content of buffer */
/* @var Layout $this */

echo $this->publishRegisteredFiles();
echo $content;
echo $this->publishRegisteredFiles(Layout::END_SCOPE);
