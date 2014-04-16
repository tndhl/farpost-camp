<?php
namespace App\User;

use Core\Database\Provider;
use Utils\User\UserEntity;

class UserProvider extends Provider
{
    /**
     * Добавление нового пользователя в БД
     *
     * @param array $params Данные пользователя
     *
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

            return TRUE;
        }

        return FALSE;
    }

    /**
     * Поиск пользователя по логину
     *
     * @param  string $login Email
     *
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
            "SELECT id, title, alt, user_field_value.value, html_tag, html_tag_type
            FROM user_field
            LEFT JOIN user_field_value ON user_field.id = user_field_value.fid AND user_field_value.uid = ?"
        );
        $xfields->execute(array($user->id));

        $user->xfields = $xfields->fetchAll();

        return $user;
    }

    /**
     * Проверка на существование пользователя
     *
     * @param  string $login E-mail
     *
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
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Активация пользователя по логину
     *
     * @param string $login Логин пользователя
     *
     * @return bool
     */
    public function activateUser($login)
    {
        $sth = $this->prepare("UPDATE user SET activate_time = NOW() WHERE login LIKE ?");

        if ($sth->execute(array($login))) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Сохранине значения поля в профиле пользователя
     *
     * @param int    $user_id ИД пользователя
     * @param string $field   Имя поля
     * @param string $value   Значение поля
     */
    public function updateUserField($user_id, $field, $value)
    {
        $available_profile_fields = ["firstname", "lastname"];

        if (!in_array($field, $available_profile_fields)) {
            return FALSE;
        }

        $querystr =
            "UPDATE user
            SET $field = ?
            WHERE id = ?
            LIMIT 1";

        $sth = $this->prepare($querystr);

        if ($sth->execute(array($value, $user_id))) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Сохранине значения доп. поля в профиле пользователя
     *
     * @param int    $user_id  ИД пользователя
     * @param int    $field_id ИД поля
     * @param string $value    Значение поля
     *
     * @return bool
     */
    public function updateUserXField($user_id, $field_id, $value)
    {
        $sth = $this->prepare(
            'INSERT INTO user_field_value (fid, uid, value)
            VALUES (:fid, :uid, :value)
            ON DUPLICATE KEY UPDATE value = :value'
        );

        $sth->bindParam(':fid', $field_id, \PDO::PARAM_INT);
        $sth->bindParam(':uid', $user_id, \PDO::PARAM_INT);
        $sth->bindParam(':value', $value, \PDO::PARAM_STR);

        if ($sth->execute()) {
            return TRUE;
        }

        return FALSE;
    }
}
