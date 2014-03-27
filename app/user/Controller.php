<?php
namespace App\User;

use Core\Services;
use Library\Mailer;
use Library\User;

class Controller extends Services
{
    /**
     * Параметры формы для валидации
     * @var array
     */
    private $required_params = array(
        "login",
        "password",
        "lastname",
        "firstname",
        "department"
    );

    /**
     * Создание хеша активации на основе данных пользователя
     * @param  array $params
     * @return string
     */
    private function createActivationHash($params)
    {
        $hash = json_encode(
            array(
                "login" => $params["login"],
                "time" => time()
            )
        );

        return urlencode(base64_encode($hash));
    }

    /**
     * Расшифровка хеша активации
     * @param $hash
     * @return array Данные пользователя для активации
     */
    private function decryptActivationHash($hash)
    {
        $hash = base64_decode(urldecode($hash));

        return (array)json_decode($hash);
    }

    /**
     * Валидация формы (\w AJAX)
     * @param array|bool $params Параметры формы
     * @return array Параметры, не прошедшие проверку
     */
    public function validate($params = false)
    {
        $params = !empty($_POST["params"]) ? json_decode($_POST["params"], true) : $params;

        if (empty($params)) {
            return false;
        }

        $result = array();

        /**
         * Проверка length > 0
         */
        foreach ($params as $attribute => $value) {
            if (in_array($attribute, $this->required_params)) {
                if (strlen($value) == 0) {
                    $result[] = $attribute;
                }
            }
        }

        /**
         * Проверка E-mail
         */
        if (($email = $params["login"])) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $result[] = "login";
            }
        }

        if (empty($_POST["params"])) {
            return $result;
        } else {
            echo json_encode($result);
            exit;
        }
    }

    /**
     * Регистрация пользователя
     */
    public function signup()
    {
        $content = $this->ViewRenderer->render("signup.index");

        if (!empty($_POST)) {
            $model = new UserProvider();

            $params = array(
                "login" => $_POST["login"],
                "password" => $_POST["password"],
                "lastname" => $_POST["lastname"],
                "firstname" => $_POST["firstname"],
                "department" => $_POST["department"]
            );

            if (count($this->validate($params)) == 0) {
                $params["password"] = password_hash($params["password"], PASSWORD_BCRYPT);

                if (!$model->isUserExists($params["login"])) {
                    if ($model->addUser($params)) {
                        $mailer = new Mailer;
                        $mailer->setReceiver($params["login"]);
                        $mailer->setSubject('Активация аккаунта FarPost Portal');

                        $hash = $this->createActivationHash($params);

                        $message = 'Для активации аккаунта, пожалуйста, пройдите по ссылке - http://portal.sashashelepov.com/user/activate?code=' . $hash;

                        $mailer->setMessage($message);

                        if ($mailer->sendEmail()) {
                            $content = $this->ViewRenderer
                                ->bindParam('login', $params["login"])
                                ->render("signup.success");
                        } else {
                            $this->displayAlertError('Ваш аккаунт создан, но, неудалось отправить сообщение для активации.');
                        }
                    } else {
                        $this->displayAlertError('Проблемы с базой данных на сервере, или нет... :(');
                    }
                } else {
                    $this->displayAlertError('Возможно, такой логин уже зарегистрирован в системе ;(');
                }
            }
        }

        $this->LayoutRenderer
            ->bindParam('content', $content)
            ->render();
    }

    /**
     * Активация аккаунта
     * @return void
     */
    public function activate()
    {
        $params = $this->decryptActivationHash($_REQUEST["code"]);

        if (!empty($params["login"])) {
            $model = new UserProvider();

            if ($model->activateUser($params["login"])) {
                $content = $this->ViewRenderer
                    ->bindParam('login', $params["login"])
                    ->render('activate.success');
            } else {
                $content = $this->ViewRenderer->render('activate.error');
            }
        } else {
            $content = $this->ViewRenderer->render('activate.error');
        }

        $this->LayoutRenderer
            ->bindParam('content', $content)
            ->render();
    }

    public function signin()
    {
        $content = $this->ViewRenderer->render('signin.index');

        if (!empty($_POST)) {
            $params = array(
                "login" => $_POST["login"],
                "password" => $_POST["password"]
            );

            $user = new User;
            $user->setParams($params);

            if ($user->userAuthentication()) {
                $content = $this->ViewRenderer->render('signin.success');
            } else {
                $this->displayAlertError('Возможно, Вы указали неверные данные. Попробуйте еще раз ;)');
            }
        }

        $this->LayoutRenderer
            ->bindParam('content', $content)
            ->render();
    }

    public function profile($login = '')
    {
        $user = new User();
        $user = $user->getSignedUser();
        $UserProvider = new UserProvider();

        if (empty($login)) {
            $user = $UserProvider->findUserByLogin($user["login"]);
            $pageTitle = "Ваш профиль";
        } else {
            $user = $UserProvider->findUserByLogin($login);
            $pageTitle = "Профиль пользователя " . $user->login;
        }

        $content = $this->ViewRenderer
            ->bindParam('user', $user)
            ->bindParam('title', $pageTitle)
            ->render('profile');

        $this->LayoutRenderer
            ->bindParam('content', $content)
            ->render();
    }
}
