<?php
namespace App\User;

class UserProvider extends \Core\Database\Provider
{
    /**
     * Добавление нового пользователя в БД
     * @param array $params Данные пользователя
     */
    public function addUser($params = array())
    {
        $attributes = implode(", ", array_keys($params));
        $values = array_values($params);
        $queryparams = str_repeat("?,", count($values) - 1);

        $sth = $this->prepare("INSERT INTO user ($attributes) VALUES ($queryparams?)");

        if ($sth->execute($values)) {
            return true;
        }

        return false;
    }

    /**
     * Поиск пользователя по логину
     * @param  string $login Email
     * @return array
     */
    public function findUserByLogin($login)
    {
       $user = $this->prepare(
            "SELECT login, firstname, lastname, department, reg_time, activate_time
            FROM user
            WHERE login LIKE :login"
        );
        $user->bindParam(":login", $login, \PDO::PARAM_STR);
        $user->execute();

        $xfields = $this->prepare(
            "SELECT id, title, alt, value, html_tag, html_tag_type
            FROM user_field
            LEFT JOIN user_field_value ON user_field.id = user_field_value.fid AND user_field_value.login = ?"
        );
        $xfields->execute(array($login));

        return array(
            "data" => $user->fetch(),
            "xfields" => $xfields->fetchAll()
        );

    }

    /**
     * Проверка на существование пользователя
     * @param  string  $login E-mail
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
