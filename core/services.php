<?php
namespace Core;

class Services
{
    protected $services = array();

    public function __construct()
    {
        $this->template = new \Core\Template(get_class($this));
    }

    public function redirect($url)
    {
        @header("Location: " . $url);
        exit;
    }
    
    public function __set($key, $value)
    {
        $this->services[$key] = $value;
    }

    public function __get($key)
    {
        if (isset($this->services[$key])) {
            return $this->services[$key];
        }
    }
}
