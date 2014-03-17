<?php
namespace Core;

abstract class Renderer {
    protected static $params;

    abstract public function render($content, $out);

    public static function getParams() {
        return self::$params;
    }

    public function bindParam($key, $value = '')
    {
        self::$params[$key] = $value;

        return $this;
    }
}
