<?php
namespace App\Index;

class Controller extends \Core\Services
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $content = $this->ViewRenderer->render('index');

        $this->LayoutRenderer
            ->bindParam('content', $content)
            ->render();
    }
}
