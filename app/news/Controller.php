<?php
namespace App\News;

use Core\Services;
use Library\Image;
use Library\User;

class Controller extends Services
{
    public function index($id = false)
    {
        if ($id) {
            return $this->getEntryById($id);
        }

        $NewsProvider = new NewsProvider();

        return $this->ViewRenderer
            ->bindParam('news', $NewsProvider->getEntryList())
            ->render('index');
    }

    private function getEntryById($id)
    {
        $NewsProvider = new NewsProvider();
        $User = new User();
        $User = $User->getCurrentUser();

        return $this->ViewRenderer
            ->bindParam('entry', $NewsProvider->getEntryById($id))
            ->bindParam('gallery', $NewsProvider->getEventPhotos($id))
            ->bindParam('user', $User)
            ->render('news');
    }

    public function gallery_upload()
    {
        $User = new User();

        if (!$User->isUserLoggedIn()) {
            exit('Access denied');
        }

        $User = $User->getCurrentUser();

        $data = json_decode($_POST["data"], true);

        $eventId = intval($data["eventId"]);
        $filename = $data["filename"];
        $filesize = $data["filesize"];
        $uniqueId = $data["uniqueId"];
        $rangeStart = $data["rangeStart"];
        $rangeEnd = $data["rangeEnd"];

        $filename = str_replace(" ", "_", $filename);
        $filename = explode(".", $filename);
        $ext = array_pop($filename);
        $filename = implode("", $filename);
        $filename .= "_" . $uniqueId . "." . $ext;

        $filepath = APP_PATH . '/public/events/id' . $eventId . '/';

        if (!is_dir($filepath)) {
            mkdir($filepath, 0755);
        }

        $tmp = fopen($_FILES[$uniqueId]["tmp_name"], "r");
        $file = fopen($filepath . $filename, "a");

        while ($data = fread($tmp, 4096)) {
            flock($file, LOCK_EX);
            fwrite($file, $data);
            flock($file, LOCK_UN);
        }

        fclose($tmp);
        fclose($file);

        if ($filesize == filesize($filepath . $filename)) {
            $NewsProvider = new NewsProvider();

            if (!$NewsProvider->addPhotoToEvent($eventId, $filename, $_SERVER["REMOTE_ADDR"], $User->id)) {
                @unlink($filepath . $filename);
            } else {
                // Создание миниатюры
                $Image = new Image($filepath . $filename);
                $Image->scaleToWidth(300);
                $Image->save($filepath . "thumb_" . $filename);
                $Image = NULL;

                print 'OK';
            }
        }

        exit();
    }
}
