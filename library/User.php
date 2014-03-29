<?php
namespace Library;

use App\User\UserProvider;
use Core\Database\Provider;
use Utils\User\UserEntity;

class User extends Provider
{
    private $params = array();

    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @return UserEntity
     */
    public function getCurrentUser()
    {
        if ($this->isUserLoggedIn()) {
            $userProvider = new UserProvider();
            $userEntity = $userProvider->findUserByLogin($this->getSignedUser()["login"]);

            return $userEntity;
        } else {
            return new UserEntity();
        }
    }

    public function isUserLoggedIn()
    {
        if (!empty($_COOKIE["hash"])) {
            $hash = $_COOKIE["hash"];

            $sth = $this->prepare(
                "SELECT u.login, s.hash, firstname, lastname, department, reg_time, activate_time
                FROM user u
                LEFT JOIN user_session s ON u.id = s.uid
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

    /**
     * @return array
     */
    public function getSignedUser()
    {
        $hash = $_COOKIE["hash"];

        $sth = $this->prepare(
            "SELECT u.login
            FROM user u
            LEFT JOIN user_session s ON u.id LIKE s.uid
            WHERE s.hash LIKE :hash"
        );

        $sth->bindParam(":hash", $hash, \PDO::PARAM_STR);
        $sth->execute();

        return $sth->fetch();
    }

    public function userAuthentication()
    {
        $query = "
            SELECT id, login, password
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
                $uid = $result["id"];
                $ip = $_SERVER["REMOTE_ADDR"];

                $query = "
                    INSERT INTO user_session (uid, hash, ip) VALUES (?, ?, INET_ATON(?))
                    ON DUPLICATE KEY UPDATE log_time = NOW()
                ";

                $sth = $this->prepare($query);
                $sth->execute(array($uid, $hash, $ip));

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
