<?php
namespace Utils\User;

use Core\Database\Provider;

class UserEntity
{
    public $id;
    public $login;
    public $firstname;
    public $lastname;
    public $department;
    public $reg_time;
    public $activate_time;
    public $xfields;

    /**
     * Проверка, есть ли роль у пользователя
     *
     * @param string $role Имя роли
     *
     * @return bool
     */
    public function hasRole($role)
    {
        $RoleModel = new RoleModel();

        return $RoleModel->hasUserRole($this->id, $role);
    }

    /**
     * Проверка, является ли пользователь супер-пользователем
     *
     * @return bool
     */
    public function isSuperUser()
    {
        $RoleModel = new RoleModel();

        return $RoleModel->isUserSU($this->id);
    }

    /**
     * Список ролей у пользователя
     *
     * @return array
     */
    public function getRoles()
    {
        $RoleModel = new RoleModel();

        return $RoleModel->getUserRoles($this->id);
    }

    /**
     * Проверка, состоит ли пользователь в очереди на книгу
     *
     * @param int $book_id ИД книги
     *
     * @return bool
     */
    public function isInBookQueue($book_id)
    {
        $pdo = new Provider();

        $sth = $pdo->prepare(
            "SELECT book_id
            FROM lib_queue
            WHERE user_id = ?
            AND book_id = ?"
        );
        $sth->execute(array($this->id, $book_id));

        if ($sth->rowCount() == 0) {
            return FALSE;
        }

        return TRUE;
    }
}
