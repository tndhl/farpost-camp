<?php
spl_autoload_register(function ($classname) {
    $classname = array_map("strtolower", explode("\\", $classname));

    $path = APP_PATH . "/" . implode("/", $classname) . '.php';

    if (file_exists($path)) {
        include $path;
    }
});
