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
                $params["password"] = password_hash($password, PASSWORD_BCRYPT);

                if (!$model->isUserExists($params["login"])) {
                    if ($model->addUser($params)) {
                        $mailer = new \Library\Mailer;
                        $mailer->setReceiver($params["login"]);
                        $mailer->setSubject('Активация аккаунта FarPost Portal');

                        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_OFB);
                        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
                        $key = "123"; // TODO

                        $url = mcrypt_encrypt(
                            MCRYPT_RIJNDAEL_128,
                            $key,
                            json_encode(
                                array(
                                    "login" => $params["login"],
                                    "time" => time()
                                )
                            ),
                            MCRYPT_MODE_OFB,
                            $iv
                        );
                        $url = base64_encode($iv . $url);
                        $message = 'Для активации аккаунта, пожалуйста, пройдите по ссылке - http://portal.sashashelepov.com/user/activate?code=' . $url;

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
        $code = base64_decode($_REQUEST["code"]);

        $key = "123"; // TODO
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_OFB);
        $iv = substr($code, 0, $iv_size);
        $code = substr($code, $iv_size);

        $params = (array) json_decode(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $code, MCRYPT_MODE_OFB, $iv));

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
