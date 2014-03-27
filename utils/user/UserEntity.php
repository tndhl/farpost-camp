<?php
namespace Utils\User;

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
}
