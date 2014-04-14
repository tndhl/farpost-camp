<?php
namespace App\News;

use Core\Database\Provider;

class NewsProvider extends Provider
{
    public function getEntryList()
    {
        $this->exec("SET lc_time_names = 'ru_RU'");

        $sth = $this->prepare(
            'SELECT id, title, content, tag, DATE_FORMAT(datetime, "%d/%m/%Y %H:%i") AS date
            FROM news
            ORDER BY id DESC'
        );

        $sth->execute();

        return $sth->fetchAll();
    }

    public function getEntryById($id)
    {
        $this->exec("SET lc_time_names = 'ru_RU'");

        $sth = $this->prepare(
            'SELECT id, title, content, tag, DATE_FORMAT(datetime, "%d/%m/%Y %H:%i") AS date
            FROM news
            WHERE id = ?'
        );

        $sth->execute(array($id));

        return $sth->fetch();
    }

    public function addPhotoToEvent($eventid, $filename, $ip, $userid)
    {
        $sth = $this->prepare(
            'INSERT INTO event_gallery (event_id, filename, ip, user_id)
            VALUES (?, ?, INET_ATON(?), ?)'
        );

        if ($sth->execute(array($eventid, $filename, $ip, $userid))) {
            return true;
        }

        return false;
    }

    public function getEventPhotos($eventid)
    {
        $sth = $this->prepare(
            'SELECT event_id, filename, INET_NTOA(ip) AS ip, user_id, concat(firstname, " ", lastname) AS user_name, DATE_FORMAT(date, "%d/%m/%Y %H:%s") AS date
            FROM event_gallery
            INNER JOIN user ON user.id = event_gallery.user_id
            WHERE event_id = ?'
        );

        $sth->execute(array($eventid));

        return $sth->fetchAll();
    }
} 