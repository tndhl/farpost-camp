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
     * Список доступных ролей
     * @return array
     */
    public function getRoles($except = array())
    {
        $WHERE = "";

        if (!empty($except)) {
            $WHERE = "WHERE id NOT IN(";
            $roles = array();

            foreach ($except as $role) {
                $roles[] .= $role["id"];
            }

            $WHERE .= implode(",", $roles);
            $WHERE .= ")";
        }

        $sth = $this->pdo->prepare(
            "SELECT id, name, description
            FROM role
            " . $WHERE . "
            ORDER BY name"
        );

        $sth->execute();

        return $sth->fetchAll();
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

    /**
     * @param $uid
     * @return array
     */
    public function getUserRoles($uid)
    {
        $sth = $this->pdo->prepare(
            "SELECT id, uid, rid, name
            FROM user_role ur
            LEFT JOIN role r ON r.id = ur.rid
            WHERE uid = ?"
        );

        $sth->execute(array($uid));

        return $sth->fetchAll();
    }

    /**
     * @param $userid
     * @param $roleid
     */
    public function addUserRole($userid, $roleid)
    {
        $sth = $this->pdo->prepare(
            'INSERT INTO user_role (uid, rid)
            VALUES (?, ?)'
        );

        if ($sth->execute(array($userid, $roleid))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $userid
     * @param $roleid
     */
    public function removeUserRole($userid, $roleid)
    {
        if ($this->isUserSU($userid)) {
            return false;
        }

        $sth = $this->pdo->prepare(
            'DELETE FROM user_role
            WHERE uid = ?
            AND rid = ?
            LIMIT 1'
        );

        if ($sth->execute(array($userid, $roleid))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Проверка, является ли пользователь супер-пользователем
     * @param $userid
     * @return bool
     */
    public function isUserSU($userid)
    {
        $sth = $this->pdo->prepare(
            'SELECT su
            FROM user
            WHERE id = ?
            LIMIT 1'
        );

        $sth->execute(array($userid));
        $user = $sth->fetch();

        return $user["su"] == 1 ? true : false;
    }
}