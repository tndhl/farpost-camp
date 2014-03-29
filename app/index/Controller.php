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
        return $this->ViewRenderer->render('index');
    }
}
