<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 3/18/14
 * Time: 6:46 AM
 */

namespace Utils\User;

use Core\Database\Provider;

class RoleModel
{
    private $pdo = null;

    public function __construct()
    {
        $this->pdo = new Provider();
    }

    /**
     * @param int $uid
     * @param string $role
     * @return bool
     */
    public function hasUserRole($uid, $role)
    {
        $sth = $this->pdo->prepare(
            'SELECT uid
            FROM user_role ur
            LEFT JOIN role r ON r.id = ur.rid
            WHERE uid = ? AND r.name LIKE ?'
        );

        $sth->execute(array($uid, $role));

        if ($sth->rowCount() == 1) {
            return true;
        } else {
            return false;
        }
    }
}