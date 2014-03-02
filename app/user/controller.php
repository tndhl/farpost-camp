<?php
namespace App\User;

class Controller extends \Core\Services
{
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
     * @return array Данные пользователя для активации
     */
    private function decryptActivationHash($hash)
    {        
        $hash = base64_decode(urldecode($hash));

        return (array) json_decode($hash);
    }

    /**
     * Валидация формы (\w AJAX)
     * @param  array $params Параметры формы
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
        if (empty($_POST)) {
            $this->template->fetch("signup.index");
        } else {
            $model = new Model;

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
                        $mailer = new \Library\Mailer;
                        $mailer->setReceiver($params["login"]);
                        $mailer->setSubject('Активация аккаунта FarPost Portal');

                        $hash = $this->createActivationHash($params);

                        $message = 'Для активации аккаунта, пожалуйста, пройдите по ссылке - http://portal.sashashelepov.com/user/activate?code=' . $hash;

                        $mailer->setMessage($message);

                        if ($mailer->sendEmail()) {
                            $this->template->bindParam("login", $params["login"]);
                            $this->template->fetch("signup.success");
                        } else {
                            $this->template->bindParam("alert",
                                $this->template->getHtml("templates.alert_error",
                                    array("text" => "Ваш аккаунт создан, но, неудалось отправить сообщение для активации.")
                                )
                            );
                            $this->template->fetch("signup.index");
                        }
                    } else {
                        $this->template->bindParam("alert",
                        $this->template->getHtml("templates.alert_error",
                                array("text" => "Проблемы с базой данных на сервере, или нет... :(")
                            )
                        );
                        $this->template->fetch("signup.index");
                    }
                } else {
                    $this->template->bindParam("alert",
                        $this->template->getHtml("templates.alert_error",
                            array("text" => "Возможно, такой логин уже зарегистрирован в системе ;(")
                        )
                    );
                    $this->template->fetch("signup.index");
                }
            }
        }
    }

    /**
     * Активация аккаунта
     * @return void 
     */
    public function activate()
    {
        $params = $this->decryptActivationHash($_REQUEST["code"]);

        if (!empty($params["login"])) {
            $model = new Model;
            
            if($model->activateUser($params["login"])) {
                $this->template->bindParam("login", $params["login"]);
                $this->template->fetch("activate.success");
            } else {
                $this->template->fetch("activate.error");
            }
        } else {
            $this->template->fetch("activate.error");
        }
    }

    public function signin()
    {
        if (empty($_POST)) {
            $this->template->fetch("signin.index");
        } else {
            $params = array(
                "login" => $_POST["login"],
                "password" => $_POST["password"]
            );

            $user = new \Library\User;
            $user->setParams($params);

            if ($user->userAuthentication()) {
                $this->template->fetch("signin.success");
            } else {
                $this->template->bindParam("alert",
                    $this->template->getHtml("templates.alert_error",
                        array("text" => "Возможно, Вы указали неверные данные. Попробуйте еще раз ;)")
                    )
                );
                $this->template->fetch("signin.index");
            }
        }
    }
}
