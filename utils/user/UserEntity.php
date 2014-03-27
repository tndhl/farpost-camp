<?php
namespace Utils\User;
use Core\Database\Provider;

/**
 * Предоставление удобного доступа к пользовательским данным.
 * + Полезные функции
 */
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
     * @param string $role
     * @return bool
     */
    public function hasRole($role)
    {
        $RoleModel = new RoleModel();

        return $RoleModel->hasUserRole($this->id, $role);
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        $RoleModel = new RoleModel();

        return $RoleModel->getUserRoles($this->id);
    }

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
            return false;
        }

        return true;
    }
}
