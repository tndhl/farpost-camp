<?php
namespace App\Index;

use Core\Services;

class Controller extends Services
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
