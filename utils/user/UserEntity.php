<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 3/18/14
 * Time: 1:06 AM
 */

namespace Utils\User;

/**
 * Предоставление удобного доступа к пользовательским данным.
 * + Полезные функции
 */
class UserEntity
{
    public $login;
    public $role;
    public $firstname;
    public $lastname;
    public $department;
    public $reg_time;
    public $activate_time;
    public $xfields;

    /**
     * @return bool
     */
    public function isAdmin()
    {
        // return !!RoleModel::isAdminRole($this->role);
        return false;
    }
}
