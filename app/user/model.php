<?php
namespace App\User;

class Model extends \Core\Database
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
