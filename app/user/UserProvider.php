<?php
namespace App\User;

use Core\Database\Provider;
use Utils\User\UserEntity;

class UserProvider extends Provider
{
    /**
     * Добавление нового пользователя в БД
     * @param array $params Данные пользователя
     * @return bool
     */
    public function addUser($params = array())
    {
        $attributes = implode(", ", array_keys($params));
        $values = array_values($params);
        $queryparams = str_repeat("?,", count($values) - 1);

        $sth = $this->prepare("INSERT INTO user ($attributes) VALUES ($queryparams?)");

        if ($sth->execute($values)) {
            $uid = $this->lastInsertId();
            $rid = 2; // Роль: Пользователь

            $sth = $this->prepare("INSERT INTO user_role (uid, rid) VALUES (?, ?)");
            $sth->execute(array($uid, $rid));

            $sth = $this->prepare("UPDATE user SET reg_ip = INET_ATON(:reg_ip) WHERE id = :uid");
            $sth->bindParam(':reg_ip', $_SERVER["REMOTE_ADDR"]);
            $sth->bindParam(':uid', $uid);
            $sth->execute();

            return true;
        }

        return false;
    }

    /**
     * Поиск пользователя по логину
     * @param  string $login Email
     * @return UserEntity
     */
    public function findUserByLogin($login)
    {
        $user = $this->prepare(
            "SELECT id, login, firstname, lastname, department, reg_time, activate_time
            FROM user
            WHERE login LIKE :login"
        );
        $user->bindParam(":login", $login, \PDO::PARAM_STR);
        $user->execute();
        $user->setFetchMode(\PDO::FETCH_CLASS, '\Utils\User\UserEntity');
        $user = $user->fetch();

        $xfields = $this->prepare(
            "SELECT id, title, alt, value, html_tag, html_tag_type
            FROM user_field
            LEFT JOIN user_field_value ON user_field.id = user_field_value.fid AND user_field_value.uid = ?"
        );
        $xfields->execute(array($user->id));

        $user->xfields = $xfields->fetchAll();

        return $user;
    }

    /**
     * Проверка на существование пользователя
     * @param  string $login E-mail
     * @return boolean
     */
    public function isUserExists($login)
    {
        $sth = $this->prepare(
            "SELECT login
            FROM user
            WHERE login LIKE :login"
        );

        $sth->bindParam(":login", $login, \PDO::PARAM_STR);
        $sth->execute();

        if ($sth->rowCount() == 1) {
            return true;
        }

        return false;
    }

    public function activateUser($login)
    {
        $sth = $this->prepare("UPDATE user SET activate_time = NOW() WHERE login LIKE ?");

        if ($sth->execute(array($login))) {
            return true;
        }

        return false;
    }
}
