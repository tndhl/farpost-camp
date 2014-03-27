<?php
namespace Utils\Library;

use Core\Database\Provider;

class QueueModel
{
    private $pdo = null;

    public function __construct()
    {
        $this->pdo = new Provider();
    }

    public function isBookOwned($book_id)
    {
        $sth = $this->pdo->prepare(
            'SELECT book_id
            FROM lib_queue
            WHERE book_id = ?
            AND owned <> 0'
        );

        $sth->execute(array($book_id));

        if ($sth->rowCount() == 0) {
            return false;
        }

        return true;
    }

    public function getBookStatus($book_id)
    {
        $sth = $this->pdo->prepare(
            'SELECT book_id
            FROM lib_queue
            WHERE book_id = ?
            AND owned = 1'
        );

        $sth->execute(array($book_id));

        if ($sth->rowCount() == 0) {
            return 'Книга свободна';
        }

        return 'На руках';
    }

    public function getBookCurrentOwner($book_id)
    {
        $sth = $this->pdo->prepare(
            "SELECT concat(firstname, ' ', lastname) as owner
            FROM lib_queue
            INNER JOIN user ON user.id = lib_queue.user_id
            WHERE book_id = ?
            AND owned = 1"
        );

        $sth->execute(array($book_id));

        return $sth->fetchColumn();
    }

    public function getBookTakingDate($book_id)
    {
        $sth = $this->pdo->prepare(
            "SELECT DATE_FORMAT(date, '%d.%m.%Y %H:%i')
            FROM lib_queue
            WHERE book_id = ?
            AND owned = 1"
        );

        $sth->execute(array($book_id));

        return $sth->fetchColumn();
    }

    public function getBookQueueLength($book_id)
    {
        $sth = $this->pdo->prepare(
            "SELECT COUNT(*)
            FROM lib_queue
            WHERE book_id = ?
            AND owned <> 1"
        );

        $sth->execute(array($book_id));

        return $sth->fetchColumn();
    }

    public function removeFromQueue($book_id, $user_id)
    {
        $sth = $this->pdo->prepare(
            "DELETE FROM lib_queue
            WHERE book_id = ?
            AND user_id = ?"
        );

        if ($sth->execute(array($book_id, $user_id))) {
            return true;
        }

        return false;
    }

    public function addToQueue($book_id, $user_id, $is_owner)
    {
        $sth = $this->pdo->prepare(
            "INSERT INTO lib_queue (book_id, user_id, owned) VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE date = NOW()"
        );

        if ($sth->execute(array($book_id, $user_id, $is_owner))) {
            return true;
        }

        return false;
    }
}