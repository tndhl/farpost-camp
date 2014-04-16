<?php
namespace App\Disk;

use Core\Database\Provider;

class DiskProvider extends Provider
{
    /**
     * Возвращает список файлов у пользователя
     *
     * @param int $user_id ИД пользователя
     *
     * @return \Utils\Disk\FileEntity[]
     */
    public function getUserFileList($user_id)
    {
        $sth = $this->prepare(
            'SELECT file_id, user_id, title, format, created, filename, filesize, shared, verified
            FROM disk_file
            WHERE user_id = ?
            ORDER BY verified DESC, file_id DESC'
        );

        $sth->execute(array($user_id));

        return $sth->fetchAll(\PDO::FETCH_CLASS, '\Utils\Disk\FileEntity');
    }

    /**
     * Поиск файла по его ИД
     *
     * @param int $file_id ИД файла
     *
     * @return \Utils\Disk\FileEntity
     */
    public function getFileById($file_id)
    {
        $sth = $this->prepare(
            'SELECT file_id, user_id, title, format, created, filename, filesize, shared, verified
            FROM disk_file
            WHERE file_id = ?'
        );

        $sth->execute(array($file_id));
        $sth->setFetchMode(\PDO::FETCH_CLASS, '\Utils\Disk\FileEntity');

        return $sth->fetch();
    }

    /**
     * Проверка, принадлежит ли файл пользователю
     *
     * @param int $file_id ИД файла
     * @param int $user_id ИД пользователя
     *
     * @return bool
     */
    public function isFileRefersToUser($file_id, $user_id)
    {
        $sth = $this->prepare(
            'SELECT title
            FROM disk_file
            WHERE file_id = ? AND user_id = ?
            AND verified = 1'
        );

        $sth->execute(array($file_id, $user_id));

        return $sth->rowCount() > 0;
    }

    /**
     * Проверка, принадлежат ли файлы пользователю
     *
     * @param array $files   Массив ИД файлов
     * @param int   $user_id ИД пользователя
     *
     * @return bool
     */
    public function isFilesReferToUser(Array $files, $user_id)
    {
        $placeholders = implode(", ", array_fill(0, count($files), '?'));

        $sth = $this->prepare(
            'SELECT title
            FROM disk_file
            WHERE file_id IN (' . $placeholders . ') AND user_id = ?
            AND verified = 1'
        );

        $params = $files;
        array_push($params, $user_id);

        $sth->execute($params);

        return $sth->rowCount() > 0;
    }

    /**
     * Проверка, находится ли файл в общем доступе
     *
     * @param int $file_id ИД файла
     *
     * @return bool
     */
    public function isFileShared($file_id)
    {
        $sth = $this->prepare(
            'SELECT shared
            FROM disk_file
            WHERE file_id = ?
            AND verified = 1'
        );

        $sth->execute(array($file_id));

        return (bool)$sth->fetchColumn();
    }

    /**
     * Удаление файла
     *
     * @param int $file_id ИД файла
     *
     * @return bool
     */
    public function removeFileById($file_id)
    {
        $sth = $this->prepare(
            'DELETE FROM disk_file
            WHERE file_id = ?'
        );

        if ($sth->execute(array($file_id))) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Добавление файла
     *
     * @param int    $userId      ИД пользователя
     * @param string $title       Название файла
     * @param string $format      Тип файла
     * @param string $filename    Имя файла
     * @param int    $filesize    Размер файла
     * @param string $REMOTE_ADDR IP адрес пользователя
     *
     * @return bool|\Utils\Disk\FileEntity
     */
    public function addFile($userId, $title, $format, $filename, $filesize, $REMOTE_ADDR)
    {
        $sth = $this->prepare(
            'INSERT INTO disk_file (user_id, title, format, filename, filesize, ip)
            VALUES (?, ?, ?, ?, ?, INET_ATON(?))'
        );

        if ($sth->execute(array($userId, $title, $format, $filename, $filesize, $REMOTE_ADDR))) {
            return $this->getFileById($this->lastInsertId());
        }

        return FALSE;
    }

    /**
     * Предоставление файла в общий доступ
     *
     * @param int $id ИД файла
     *
     * @return bool
     */
    public function shareFileById($id)
    {
        if (is_array($id)) {
            $placeholders = implode(", ", array_fill(0, count($id), '?'));
            $WHERE = "WHERE file_id IN ($placeholders)";
        } else {
            $id = array($id);
            $WHERE = "WHERE file_id = ?";
        }

        $sth = $this->prepare(
            'UPDATE disk_file
            SET shared = 1
            ' . $WHERE
        );

        if ($sth->execute($id)) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Возвращает размер занятого места на диске пользователя
     *
     * @param int $user_id ИД пользователя
     *
     * @return int Размер в байтах
     */
    public function getUserDiskSize($user_id)
    {
        $sth = $this->prepare(
            'SELECT SUM(filesize)
            FROM disk_file
            WHERE user_id = ?
            AND verified = 1'
        );

        $sth->execute(array($user_id));

        return $sth->fetchColumn();
    }

    /**
     * Возвращает список файлов находящихся в общем доступе
     *
     * @return \Utils\Disk\FileEntity[]
     */
    public function getSharedFileList()
    {
        $sth = $this->prepare(
            'SELECT file_id, user_id, title, format, created, filename, filesize, shared, verified
            FROM disk_file
            WHERE shared = 1
            AND verified = 1
            ORDER BY file_id DESC'
        );

        $sth->execute();

        return $sth->fetchAll(\PDO::FETCH_CLASS, '\Utils\Disk\FileEntity');
    }

    /**
     * Возвращает список файлов ожидающих проверку
     *
     * @return \Utils\Disk\FileEntity
     */
    public function getPendingFileList()
    {
        $sth = $this->prepare(
            'SELECT file_id, user_id, title, format, created, filename, filesize, shared, verified
            FROM disk_file
            WHERE verified = 0
            ORDER BY file_id DESC'
        );

        $sth->execute();

        return $sth->fetchAll(\PDO::FETCH_CLASS, '\Utils\Disk\FileEntity');
    }

    /**
     * Одобряет файл
     * 
     * @param int $id ИД файла
     *
     * @return bool
     */
    public function verifyFileById($id)
    {
        $sth = $this->prepare(
            'UPDATE disk_file
            SET verified = 1
            WHERE file_id = ?'
        );

        if ($sth->execute(array($id))) {
            return TRUE;
        }

        return FALSE;
    }
} 