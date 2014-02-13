<?php
namespace App\User;

class Controller extends \Core\Services
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Регистрация пользователя
     */
    public function signup()
    {
        $this->template->fetch("signup.index");
    }
}
