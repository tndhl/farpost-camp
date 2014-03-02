<?php
namespace Core;

class Template
{
    private $params = array();
    private $component;

    public function __construct($component)
    {
        $this->component = explode("\\", strtolower(str_replace("\Controller", "", $component)));
        array_shift($this->component);
        $this->component = implode("/", $this->component);
    }

    public function bindParam($key, $value)
    {
        $this->params[$key] = $value;
    }

    public function cleanParams()
    {
        $this->params = array();
    }

    public function removeParam($key)
    {
        if (isset($this->params[$key])) {
            unset($this->params[$key]);
        }
    }

    public function getHtml($tpl, $params = array())
    {
        $tpl = explode(".", $tpl);
        $tpl = implode("/", $tpl) . ".php";

        try {
            if (!file_exists($tpl)) {
                throw new \Exception("Cannot find template");
            }

            ob_start();
            require_once $tpl;
            $templateHtml = ob_get_contents();
            ob_end_clean();

            return $templateHtml;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function fetch($view)
    {
        $view = APP_PATH . '/app/' . $this->component . '/views/' . $view . '.php';
        $params = $this->params;

        try {
            if (!file_exists($view)) {
                throw new \Exception("Unable to load view file.");
            }

            ob_start();
            require_once $view;
            $ComponentData = ob_get_contents();
            ob_get_clean();

            require_once APP_PATH . '/templates/default.php';
            $template = ob_get_contents();

            $template = str_replace("{component}", $ComponentData, $template);
            ob_end_clean();

            echo $template;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
