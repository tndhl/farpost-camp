<?php
namespace Library;

class User extends \Core\Database
{
    private $params = array();
    private $messages = array();

    public function getMessages()
    {
        return $this->messages;
    }

    public function setParams($params)
    {
        $this->params = $params;
    }

    public function isUserLoggedIn()
    {
        if (!empty($_COOKIE["uid"]) && !empty($_COOKIE["hash"])) {
            $uid = $_COOKIE["uid"];
            $hash = $_COOKIE["hash"];

            $sth = $this->prepare(
                "SELECT uid, email, password, hash
                FROM user
                WHERE uid = :uid
                AND validated = 1
                LIMIT 1"
            );

            $sth->bindParam(":uid", $uid, PDO::PARAM_INT);
            $sth->execute();

            $result = $sth->fetch();

            if ($result["hash"] == $hash) {
                return true;
            }
        }

        return false;
    }

    public function isUserExists()
    {
        $sth = $this->prepare(
            "SELECT email
            FROM user
            WHERE email LIKE :email"
        );

        $sth->bindParam(":email", $this->params["userEmail"], PDO::PARAM_STR);
        $sth->execute();

        if ($sth->rowCount() == 1) {
            return true;
        }
    }

    public function getUserData()
    {
        $uid = $_COOKIE["uid"];

        $sth = $this->prepare(
            "SELECT uid, email, password
            FROM user
            WHERE uid = :uid
            LIMIT 1"
        );

        $sth->bindParam(":uid", $uid, PDO::PARAM_INT);
        $sth->execute();

        return $sth->fetch();
    }

    public function isUserFormValid()
    {
        if (!filter_var($this->params["userEmail"], FILTER_VALIDATE_EMAIL)) {
            $this->messages[] = "Неверный формат почтового адреса.";
        } elseif (empty($this->params["userPassword"])) {
            $this->messages[] = "Не указан пароль.";
        } else {
            return true;
        }
    }

    public function userAuthentication()
    {
        $query = "
            SELECT uid, email, password
            FROM user
            WHERE email LIKE :email
            AND validated = 1
            LIMIT 1
        ";

        $sth = $this->prepare($query);
        $sth->bindParam(':email', $this->params["userEmail"], PDO::PARAM_STR);
        $sth->execute();

        $result = $sth->fetch();

        if (!empty($result["email"])) {
            if (password_verify($this->params["userPassword"], $result["password"])) {   
                $hash = substr($result["password"], 10, 20);

                $query = "
                    UPDATE user
                    SET hash = :hash
                    WHERE uid = :uid
                ";

                $sth = $this->prepare($query);
                $sth->bindParam(":uid", $result["uid"], PDO::PARAM_INT);
                $sth->bindParam(":hash", $hash, PDO::PARAM_STR);
                $sth->execute();

                @setcookie("uid", $result["uid"], time() + 3600, "/");
                @setcookie("hash", $hash, time() + 3600, "/");

                return true;
            } else {
                $this->messages[] = "Неверно указан пароль.";
            }
        } else {
            $this->messages[] = "Пользователя с таким почтовым адресом не существует.";
        }

        return false;
    }

    public function userRegistration()
    {
        if (!$this->isUserExists()) {
            $query = "
                INSERT INTO user (email, password)
                VALUES (:email, :password)
            ";

            $sth = $this->prepare($query);
            $sth->bindParam(":email", $this->params["userEmail"], PDO::PARAM_STR);
            $sth->bindParam(":password", password_hash($this->params["userPassword"], PASSWORD_BCRYPT), PDO::PARAM_STR);

            if ($sth->execute()) {
                $validateData = $this->params["userEmail"];
                $validateUrl = "http://backend-php.sashashelepov.com/user/validate/" . base64_encode($validateData);
                $validateText = "Для активации аккаунта, пожалуйста, пройдите по следующей ссылке: " . $validateUrl;

                if (mail($this->params["userEmail"], "Активация аккаунта на Backend-PHP", $validateText)) {
                    $this->messages[] = "На Вашу почту отправлено сообщение для активации аккаунта.";
                    return true;
                } else {
                    $this->messages[] = "Невозможно отправить сообщение на почту. Пожалуйста, попробуйте позже.";
                }
            } else {
                $this->messages[] = "Возможно, пользователь с таким почтовым адресом уже зарегистрирован!";
            }
        } else {
            $this->messages[] = "Пользователь с таким почтовым адресом уже существует.";
        }

        return false;
    }

    public function userValidate($email)
    {
        $email = base64_decode($email);

        $query = "
            SELECT uid, email, password
            FROM user
            WHERE email LIKE :email
            LIMIT 1
        ";

        $sth = $this->prepare($query);
        $sth->bindParam(':email', $email, PDO::PARAM_STR);
        $sth->execute();

        $result = $sth->fetch();

        if (!empty($result["password"])) {
            $sth = $this->prepare(
                "UPDATE user
                SET validated = 1
                WHERE email LIKE :email"
            );
            $sth->bindParam(":email", $email, PDO::PARAM_STR);
            $sth->execute();

            return true;
        }
    }

    public function userLogout()
    {
        @setcookie("uid", "", time() - 3600, "/");
        @setcookie("hash", "", time() - 3600, "/");
    }
}
