<?php
namespace Library;

class User extends \Core\Database\Provider
{
    private $params = array();

    public function setParams($params)
    {
        $this->params = $params;
    }

    public function isUserLoggedIn()
    {
        if (!empty($_COOKIE["hash"])) {
            $hash = $_COOKIE["hash"];

            $sth = $this->prepare(
                "SELECT u.login, s.hash, firstname, lastname, department, reg_time, activate_time
                FROM user u
                LEFT JOIN user_session s ON u.login LIKE s.login
                WHERE s.hash LIKE :hash
                LIMIT 1"
            );

            $sth->bindParam(":hash", $hash, \PDO::PARAM_STR);
            $sth->execute();

            if ($sth->rowCount() == 1) {
                return true;
            }
        }

        return false;
    }

    public function getUserData()
    {
        $hash = $_COOKIE["hash"];

        $sth = $this->prepare(
            "SELECT u.login, role, firstname, lastname, department, reg_time, activate_time
            FROM user u
            LEFT JOIN user_session s ON u.login LIKE s.login
            WHERE s.hash LIKE :hash"
        );

        $sth->bindParam(":hash", $hash, \PDO::PARAM_STR);
        $sth->execute();

        return $sth->fetch();
    }

    public function userAuthentication()
    {
        $query = "
            SELECT login, password
            FROM user
            WHERE login LIKE :login
            AND activate_time IS NOT NULL
            LIMIT 1
        ";

        $sth = $this->prepare($query);
        $sth->bindParam(':login', $this->params["login"], \PDO::PARAM_STR);
        $sth->execute();

        $result = $sth->fetch();

        if (!empty($result["login"])) {
            if (password_verify($this->params["password"], $result["password"])) {   
                $hash = substr($result["password"], 10, 20);
                $login = $this->params["login"];

                $query = "
                    INSERT INTO user_session (login, hash) VALUES (?, ?)
                    ON DUPLICATE KEY UPDATE log_time = NOW()
                ";

                $sth = $this->prepare($query);
                $sth->execute(array($login, $hash));

                @setcookie("hash", $hash, time() + 3600, "/");

                return true;
            }
        }

        return false;
    }

    public function userLogout()
    {
        $hash = $_COOKIE["hash"];
        $sth = $this->prepare("DELETE FROM user_session WHERE hash LIKE ?");
        $sth->execute(array($hash));
        
        @setcookie("hash", "", time() - 3600, "/");
    }
}
