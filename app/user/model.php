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
        $attributes = array();
        $attributes_param = array();
        $values = array();

        foreach ($params as $param) {
            $attributes[] = $param["name"];
            $values[] = $param["value"];

            $attributes_param[] = "?";
        }

        $attributes = implode(", ", $attributes);
        $attributes_param = implode(", ", $attributes_param);

        $sth = $this->prepare("INSERT INTO user ($attributes) VALUES ($attributes_param)");

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
