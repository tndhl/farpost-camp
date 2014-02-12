<?php
namespace Core;

define("APP_PATH", realpath(dirname(__FILE__)));

require_once APP_PATH . '/core/autoloader.php';

$dispatcher = new \Core\Dispatcher();
$dispatcher->Run();
