<?php
namespace App\User;

use Core\Services;
use Library\Mailer;
use Library\User;
use Utils\User\RoleModel;

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
     *
     * @param  array $params
     *
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
     *
     * @param $hash
     *
     * @return array Данные пользователя для активации
     */
    private function decryptActivationHash($hash)
    {
        $hash = base64_decode(urldecode($hash));

        return (array)json_decode($hash);
    }

    /**
     * Валидация формы (\w AJAX)
     *
     * @param array|bool $params Параметры формы
     *
     * @return array Параметры, не прошедшие проверку
     */
    public function validate($params = FALSE)
    {
        $params = !empty($_POST["params"]) ? json_decode($_POST["params"], TRUE) : $params;

        if (empty($params)) {
            return FALSE;
        }

        $result = array();

        /**
         * Проверка length > 0
         */
        foreach ($params as $attribute => $value) {
            if (in_array($attribute, $this->required_params)) {
                if (strlen($value) == 0) {
                    $result[] = array("element" => $attribute);
                }
            }
        }

        /**
         * Проверка E-mail
         */
        if (($email = $params["login"])) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $result[] = array("element" => "login", "error" => "Неверный формат почтового адреса");
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

        return $content;
    }

    /**
     * Активация аккаунта
     * @return string
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

        return $content;
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

        return $content;
    }

    public function profile($login = '')
    {
        $User = new User();

        $user = $User->getSignedUserLogin();
        $UserProvider = new UserProvider();

        if (empty($login)) {
            if (!$User->isUserLoggedIn()) {
                return $this->ViewRenderer->render('nologin');
            }

            $user = $UserProvider->findUserByLogin($user);
            $pageTitle = "Ваш профиль";
        } else {
            $user = $UserProvider->findUserByLogin($login);
            $pageTitle = "Профиль пользователя " . $user->login;
        }

        return $this->ViewRenderer
            ->bindParam('profile', $user)
            ->bindParam('user', $User->getCurrentUser())
            ->bindParam('title', $pageTitle)
            ->render('profile');
    }

    /**
     * @AJAX
     * Добавление роли пользователю
     */
    public function addrole()
    {
        $RoleModel = new RoleModel();
        $CurrentUser = new User();

        /** @var int $userId ИД пользователя, кому присвоить роль */
        $userId = $_REQUEST["userid"];

        if ($CurrentUser->isUserLoggedIn()) {
            $CurrentUser = $CurrentUser->getCurrentUser();

            if (!$RoleModel->hasUserRole($CurrentUser->id, 'Администратор')) {
                exit();
            }
        } else {
            exit();
        }


        if (empty($_POST)) {
            $form = $this->ViewRenderer
                ->bindParam('roles', $RoleModel->getRoles($RoleModel->getUserRoles($userId)))
                ->render('ajax.addrole');

            print json_encode(array(
                "form" => $form
            ));

            exit();
        } else {
            /** @var int $roleid ИД роли, которую нужно добавить */
            $roleId = $_POST["roleid"];

            if ($RoleModel->addUserRole($userId, $roleId)) {
                print "OK";
            }

            exit();
        }
    }

    /**
     * @AJAX
     * Удаление роли у пользователя
     */
    public function removerole()
    {
        $RoleModel = new RoleModel();
        $CurrentUser = new User();

        if ($CurrentUser->isUserLoggedIn()) {
            $CurrentUser = $CurrentUser->getCurrentUser();

            if (!$RoleModel->hasUserRole($CurrentUser->id, 'Администратор')) {
                exit();
            }
        } else {
            exit();
        }

        if (!empty($_POST)) {
            $userId = $_POST["userid"];
            $roleId = $_POST["roleid"];

            if ($RoleModel->removeUserRole($userId, $roleId)) {
                print "OK";
            }
        }

        exit();
    }

    /**
     * @AJAX
     *
     * Сохранить значение поля в профиле
     */
    public function save_field()
    {
        $provider = new UserProvider();
        $user = (new User())->getCurrentUser();

        $data = $_POST["data"];

        if ($user->id != $data[0]["user_id"]) {
            if (!$user->hasRole('Администратор')) {
                echo json_encode(["error" => "У вас нет прав для выполнения этой операции"]);
                exit();
            }
        }

        foreach ($data as $field) {
            if ($field["field_id"]) {
                if (!$provider->updateUserXField($field["user_id"], $field["field_id"], $field["value"])) {
                    echo json_encode(["error" => "Возникли ошибки при обновлении поля: " . $field["title"]]);
                }
            } else {
                if (!$provider->updateUserField($field["user_id"], $field["name"], $field["value"])) {
                    echo json_encode(["error" => "Возникли ошибки при обновлении поля: " . $field["title"]]);
                }
            }
        }

        echo json_encode(["message" => "Данные профиля успешно обновлены"]);
        exit();
    }
}
