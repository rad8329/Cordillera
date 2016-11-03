<?php

spl_autoload_register(function ($class) {

    // project-specific namespace prefix
    $prefix = 'cordillera\\';

    // does the class use the namespace prefix?
    $len = strlen($prefix);

    if (strncmp($prefix, $class, $len) !== 0) {
        // replace the namespace prefix with the base directory, replace namespace
        // separators with directory separators in the relative class name, append
        // with .php
        $file = CORDILLERA_APP_DIR.str_replace('\\', DS, $class).'.php';

        if (file_exists($file)) {
            require $file;
        }
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = CORDILLERA_DIR.str_replace('\\', DS, $relative_class).'.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});
