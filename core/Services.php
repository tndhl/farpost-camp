<?php
namespace Core;

use Library\User;

abstract class Services
{
    /**
     * @var string Alert HTML
     */
    private $alert;

    protected $services = array();

    public function __construct()
    {
        $this->ViewRenderer = new ViewRenderer(get_class($this));

        if (isset($_GET["logout"])) {
            $user = new User;
            $user->userLogout();

            $this->redirect("/");
        }
    }

    /**
     * Установка сообщения для пользователя
     * @param string $type success|error
     * @param string $message Сообщение
     * @return void
     */
    public function setAlert($type, $message)
    {
        $LayoutRenderer = new LayoutRenderer();

        $this->alert = $LayoutRenderer
            ->bindParam('text', $message)
            ->render('alert_' . $type, false);
    }

    public function getAlert()
    {
        return $this->alert;
    }

    /**
     * @deprecated
     */
    public function displayAlertError($message)
    {
        $this->setAlert('error', $message);
    }

    /**
     * @deprecated
     */
    public function displayAlertSuccess($message)
    {
        $this->setAlert('success', $message);
    }

    public function redirect($url)
    {
        @header("Location: " . $url);
        exit;
    }

    public function __set($key, $value)
    {
        $this->services[$key] = $value;
    }

    public function __get($key)
    {
        if (isset($this->services[$key])) {
            return $this->services[$key];
        }

        return false;
    }
}
