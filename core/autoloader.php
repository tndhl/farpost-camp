<?php
spl_autoload_register(function ($classname) {
    $path = explode("\\", $classname);
    $file = array_pop($path) . '.php';

    $path = APP_PATH . "/" . implode("/", array_map("strtolower", $path)) . '/';

    if (file_exists($path . $file)) {
        require_once $path . $file;
        return true;
    }

    return false;
});
