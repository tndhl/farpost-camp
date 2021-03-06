<?php
namespace Core;

class ViewRenderer extends Renderer
{
    private $componentViewDirectory;

    public function __construct($component)
    {
        $this->componentViewDirectory = explode("\\", strtolower(str_replace('\Controller', '', $component)));
        $this->componentViewDirectory[] = "views";
        $this->componentViewDirectory = APP_PATH . '/' . implode("/", $this->componentViewDirectory);
    }

    /**
     * @param string $view Имя файла представления
     * @param bool $out Если TRUE, выведет полученный HTML на экран. Иначе, вернет HTML.
     * @return string|void
     */
    public function render($view, $out = false)
    {
        if (!empty(self::$params)) {
            foreach (self::$params as $key => $value) {
                $$key = $value;
            }
        }

        ob_start();
        require_once $this->componentViewDirectory . '/' . $view . '.html.php';
        $view = ob_get_contents();
        ob_end_clean();

        if ($out) {
            echo $view;
        } else {
            return $view;
        }
    }
}
