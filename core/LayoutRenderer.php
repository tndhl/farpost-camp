<?php
namespace Core;

class LayoutRenderer extends Renderer
{
    public function render($template = 'default', $out = true)
    {
        $params = $this->params;

        ob_start();
        require_once APP_PATH . '/templates/' . $template . '.php';
        $template = ob_get_contents();
        $template = str_replace("{error}", $params["error"], $template);
        $template = str_replace("{content}", $params["content"], $template);
        ob_end_clean();

        if ($out) {
            echo $template;
        } else {
            return $template;
        }
    }
}
