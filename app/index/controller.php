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
        $this->template->fetch("index");
    }
}
