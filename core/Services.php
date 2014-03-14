<?php
namespace Core;

abstract class Services
{
    protected $services = array();

    public function __construct()
    {
        $this->LayoutRenderer = new \Core\LayoutRenderer();
        $this->ViewRenderer = new \Core\ViewRenderer(get_class($this));

        $user = new \Library\User;

        if ($user->isUserLoggedIn()) {
            $this->LayoutRenderer
                ->bindParam("user", 
                    $this->LayoutRenderer
                        ->bindParam('profile', $user->getUserData())
                        ->render('user_logged', false));
        } else {
            $this->LayoutRenderer
                ->bindParam("user", 
                    $this->LayoutRenderer->render('user_links', false)
                );
        }

        if (isset($_GET["logout"])) {
            $user->userLogout();
            $this->redirect("/");
        }
    }

    /**
     * Вывод ошибки пользователю
     * @param  string $message ошибка
     * @return  void
     */
    public function displayAlertError($message)
    {
        $this->LayoutRenderer
            ->bindParam('error', 
                $this->LayoutRenderer
                    ->bindParam('text', $message)
                    ->render('alert_error', false));
    }

    public function displayAlertSuccess($message)
    {
        $this->LayoutRenderer
            ->bindParam('error', 
                $this->LayoutRenderer
                    ->bindParam('text', $message)
                    ->render('alert_success', false));
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
    }
}
