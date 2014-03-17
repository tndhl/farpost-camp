<?php
namespace Core;

use App\User\UserProvider;
use Library\User;

abstract class Services
{
    protected $services = array();

    public function __construct()
    {
        $this->LayoutRenderer = new LayoutRenderer();
        $this->ViewRenderer = new ViewRenderer(get_class($this));

        $user = new User;

        if ($user->isUserLoggedIn()) {
            $userProvider = new UserProvider();
            $userEntity = $userProvider->findUserByLogin($user->getSignedUser()["login"]);

            $this->LayoutRenderer
                ->bindParam("user", $userEntity)
                ->bindParam("userlinks", $this->LayoutRenderer->render('user_logged', false));
        } else {
            $this->LayoutRenderer
                ->bindParam('userlinks', $this->LayoutRenderer->render('user_links', false))
                ->bindParam('user', null);
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

        return false;
    }
}
