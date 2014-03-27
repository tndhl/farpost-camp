<?php
namespace Core;

class ViewRenderer extends Renderer
{
    private $componentViewDirectory;

    public function __construct($component)
    {
        $this->componentViewDirectory = explode("\\", strtolower(str_replace("\Controller", "", $component)));
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
        $params = self::$params;
        $globals = LayoutRenderer::getParams();

        ob_start();
        require_once $this->componentViewDirectory . '/' . $view . '.php';
        $view = ob_get_contents();
        ob_end_clean();

        if ($out) {
            echo $view;
        } else {
            return $view;
        }
    }
}
