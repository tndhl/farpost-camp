<?php
namespace App\Chat;

use Core\Database\Provider;

class ChatProvider extends Provider
{
    public function addMessage($user_id, $message, $ip)
    {
        $sth = $this->prepare(
            'INSERT INTO chat_message(user_id, message, ip)
            VALUES (?, ?, INET_ATON(?))'
        );

        if ($sth->execute(array($user_id, $message, $ip))) {
            return $this->lastInsertId();
        }

        return false;
    }

    public function hasNewMessages($last_msg_id)
    {
        $sth = $this->prepare(
            'SELECT id
            FROM chat_message
            WHERE id > ?
            ORDER BY id'
        );

        $sth->execute(array($last_msg_id));

        return $sth->rowCount() > 0;
    }

    public function getMessageById($id)
    {
        $this->exec("SET lc_time_names = 'ru_RU'");

        $sth = $this->prepare(
            'SELECT chat_message.id, concat(firstname, " ", lastname) as username, message, DATE_FORMAT(datetime, "%W, %d/%m/%Y %H:%s") AS datetime
            FROM chat_message
            INNER JOIN user ON user.id = user_id
            WHERE chat_message.id = ?'
        );

        $sth->execute(array($id));

        return $sth->fetch();
    }

    public function getMessageList($last_msg_id = false)
    {
        $this->exec("SET lc_time_names = 'ru_RU'");

        if ($last_msg_id) {
            $WHERE = "WHERE chat_message.id > :last_msg_id";
        } else {
            $WHERE = "";
        }

        $sth = $this->prepare(
            'SELECT chat_message.id, concat(firstname, " ", lastname) as username, message, DATE_FORMAT(datetime, "%W, %d/%m/%Y %H:%s") AS datetime
            FROM chat_message
            INNER JOIN user ON user.id = user_id
            ' . $WHERE . '
            ORDER BY chat_message.id'
        );

        if ($last_msg_id) {
            $sth->bindParam(':last_msg_id', $last_msg_id, \PDO::PARAM_INT);
        }

        $sth->execute();

        return $sth->fetchAll();
    }
} 