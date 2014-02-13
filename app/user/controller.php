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
        foreach ($params as $param) {
            if (in_array($param["name"], $this->required_params)) {
                if (strlen($param["value"]) == 0) {
                    $result[] = $param["name"];
                }
            }
        }

        /**
         * Проверка E-mail
         */
        if (($email = $this->recursive_array_search("login", $params))) {
            if (!filter_var($email["param"]["value"], FILTER_VALIDATE_EMAIL)) {
                $result[] = $email["param"]["name"];
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
                array("name" => "login", "value" => $_POST["login"]),
                array("name" => "password", "value" => $_POST["password"]),
                array("name" => "lastname", "value" => $_POST["lastname"]),
                array("name" => "firstname", "value" => $_POST["firstname"]),
                array("name" => "department", "value" => $_POST["department"])
            );

            if (count($this->validate($params)) == 0) {
                $params[1]["value"] = password_hash($password, PASSWORD_BCRYPT);

                if (!$model->isUserExists($params[0]["value"])) {
                    if ($model->addUser($params)) {
                        $mailer = new \Library\Mailer;
                        $mailer->setReceiver($params[0]["value"]);
                        $mailer->setSubject('Активация аккаунта FarPost Portal');

                        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_OFB);
                        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
                        $key = "123"; // TODO

                        $url = mcrypt_encrypt(
                            MCRYPT_RIJNDAEL_128, 
                            $key, 
                            json_encode(
                                array(
                                    "login" => $params[0]["value"],
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
                            // TODO: OK
                        } // TODO: Проблемы с мейлером
                    } // TODO: Проблемы с БД
                } // TODO: Логин уже существует
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
            $model->activateUser($params["login"]);
        }
    }
}
