<?php
namespace Core;

class LayoutRenderer extends Renderer
{
    /**
     * @param string $template Имя файла шаблона
     * @param bool   $out      Если TRUE, выведет полученный HTML на экран. Иначе, вернет HTML.
     *
     * @return string|void
     */
    public function render($template = 'default', $out = TRUE)
    {
        if (!empty(self::$params)) {
            foreach (self::$params as $key => $value) {
                $$key = $value;
            }
        }

        ob_start();
        require_once APP_PATH . '/templates/' . $template . '.html.php';
        $template = ob_get_contents();
        $template = str_replace("{alert}", $alert, $template);
        $template = str_replace("{content}", $content, $template);
        ob_end_clean();

        if ($out) {
            echo $template;
        } else {
            return $template;
        }
    }
}
