<?php
namespace Core;

abstract class Renderer {
    protected $params;

    abstract public function render($content, $out);

    public function bindParam($key, $value = '')
    {
        $this->params[$key] = $value;

        return $this;
    }
}
