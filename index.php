<?php
namespace Core;

error_reporting(0);
@ini_set('display_errors', 0);

define("APP_PATH", realpath(dirname(__FILE__)));

require_once APP_PATH . '/core/autoloader.php';

$dispatcher = new Dispatcher();
$dispatcher->Run();
