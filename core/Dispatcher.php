<?php
namespace Core;

class Dispatcher
{
    private $default_request = "index/index";
    private $default_component_method = "index";

    public function parseRequest(&$component, &$method, &$args)
    {
        $request = (isset($_GET["q"])) ? trim($_GET["q"], "/\\") : $this->default_request;
        $request = explode("/", $request);

        $component = array_shift($request);
        $method = (isset($request[0]) && !is_numeric($request[0])) ? array_shift($request) : $this->default_component_method;
        $args = $request;
    }

    public function run()
    {
        $this->parseRequest($component, $method, $args);

        try {
            $component = "\\App\\$component\\Controller";

            if (!class_exists($component)) {
                throw new \Exception("Cannot load component");
            }

            /** @var $component Services */
            $component = new $component();

            if (!is_callable(array($component, $method))) {
                throw new \Exception("Component controller has no requested method.");
            }

            $content = call_user_func_array(array($component, $method), $args);
            $alert = $component->getAlert();

            $ContentRenderer = new ContentRenderer();
            $ContentRenderer->setContent($content);
            $ContentRenderer->setAlert($alert);

            $ContentRenderer->render();
        } catch (\Exception $e) {
            print $e->getMessage();
            exit;
        }
    }
}
