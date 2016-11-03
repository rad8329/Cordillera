<?php

defined('DS') or define('DS', DIRECTORY_SEPARATOR);

if (!defined('CORDILLERA_APP_DIR')) {
    die('CORDILLERA_APP_DIR is not defined');
}

defined('CORDILLERA_APP_DIR') or define('CORDILLERA_APP_DIR', realpath(str_replace('\\', '/', dirname(__FILE__).'/../../../').DS.'app').DS);
defined('CORDILLERA_DIR') or define('CORDILLERA_DIR', realpath(str_replace('\\', '/', dirname(__FILE__).'/..').DS.'cordillera').DS);
defined('CORDILLERA_DEBUG') or define('CORDILLERA_DEBUG', true);

require CORDILLERA_DIR.'lazycoding.php';
require CORDILLERA_DIR.'autoload.php';

function exception_error_handler($errno, $errstr, $errfile, $errline)
{
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

set_error_handler('exception_error_handler');
