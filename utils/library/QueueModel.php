<?php
namespace Utils\Library;

use Core\Database\Provider;

class QueueModel
{
    private $pdo = NULL;

    public function __construct()
    {
        $this->pdo = new Provider();
    }

    /**
     * Проверка, на руках ли книга
     *
     * @param int $book_id ИД книги
     *
     * @return bool
     */
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
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Текущий статус книги
     *
     * @param int $book_id ИД книги
     *
     * @return string
     */
    public function getBookStatus($book_id)
    {
        if (!$this->isBookOwned($book_id)) {
            return 'Книга свободна';
        }

        return 'На руках';
    }

    /**
     * Имя текущего владелеца книги
     *
     * @param int $book_id ИД книги
     *
     * @return string
     */
    public function getBookOwnerName($book_id)
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

    /**
     * ИД текущего владельца книги
     *
     * @param int $book_id ИД книги
     *
     * @return string
     */
    public function getBookOwnerId($book_id)
    {
        $sth = $this->pdo->prepare(
            "SELECT user_id
            FROM lib_queue
            WHERE book_id = ?
            AND owned = 1"
        );

        $sth->execute(array($book_id));

        return $sth->fetchColumn();
    }

    /**
     * Дата взятия книги
     *
     * @param int $book_id ИД книги
     *
     * @return string
     */
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

    /**
     * Длина очереди на книгу
     *
     * @param int $book_id ИД книги
     *
     * @return string
     */
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

    /**
     * Установить нового владельца книги
     *
     * @param int $book_id ИД книги
     * @param int $user_id ИД пользователя
     */
    public function setBookOwner($book_id, $user_id)
    {
        $sth = $this->pdo->prepare(
            "UPDATE lib_queue
            SET owned = 1
            WHERE book_id = ?
            AND user_id = ?"
        );

        $sth->execute(array($book_id, $user_id));
    }

    /**
     * Получить ИД следующего пользователя в очереди на книгу
     *
     * @param int $book_id ИД книги
     *
     * @return int
     */
    public function getNextBookOwner($book_id)
    {
        $sth = $this->pdo->prepare(
            "SELECT user_id
            FROM lib_queue
            WHERE owned = 0
            AND book_id = ?
            ORDER BY date
            LIMIT 1"
        );

        $sth->execute(array($book_id));

        return $sth->fetchColumn();
    }

    /**
     * Удалить пользователя из очереди на книгу
     *
     * @param int $book_id ИД книги
     * @param int $user_id ИД пользователя
     *
     * @return bool
     */
    public function removeFromQueue($book_id, $user_id)
    {
        if ($this->isBookOwned($book_id)) {
            if ($this->getBookOwnerId($book_id) == $user_id) {
                if (($owner_id = $this->getNextBookOwner($book_id)) != FALSE) {
                    $this->setBookOwner($book_id, $owner_id);
                }
            }
        }

        $sth = $this->pdo->prepare(
            "DELETE FROM lib_queue
            WHERE book_id = ?
            AND user_id = ?"
        );

        if ($sth->execute(array($book_id, $user_id))) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Добавить пользователя в очередь на книгу
     *
     * @param int $book_id  ИД книги
     * @param int $user_id  ИД пользователя
     * @param int $is_owner Владелец?
     *
     * @return bool
     */
    public function addToQueue($book_id, $user_id, $is_owner = 0)
    {
        $sth = $this->pdo->prepare(
            "INSERT INTO lib_queue (book_id, user_id, owned) VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE date = NOW()"
        );

        if ($sth->execute(array($book_id, $user_id, $is_owner))) {
            return TRUE;
        }

        return FALSE;
    }
}