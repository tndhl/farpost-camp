<?php
namespace Core;

class LayoutRenderer extends Renderer
{
    /**
     * @param string $template Имя файла шаблона
     * @param bool $out Если TRUE, выведет полученный HTML на экран. Иначе, вернет HTML.
     * @return string|void
     */
    public function render($template = 'default', $out = true)
    {
        $params = self::$params;

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
