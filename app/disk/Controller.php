<?php
namespace App\Disk;

use Core\Services;
use Library\User;

class Controller extends Services
{
    public function index()
    {
        $user = (new User())->getCurrentUser();

        if (empty($user->id)) {
            return $this->shared();
        } else {
            return $this->my();
        }
    }

    public function feed()
    {
        $user = (new User())->getCurrentUser();
        $disk = new DiskProvider();

        if (!$user->hasRole('Кладовщик')) {
            $this->setAlert('error', 'У вас нет доступа к этой странице.');
            return FALSE;
        }

        $content = $this->ViewRenderer
            ->bindParam('files', $disk->getPendingFileList())
            ->render('feed');

        return $this->buildContainer($content, 'disk/feed');
    }

    public function my()
    {
        $disk = new DiskProvider();
        $user = (new User())->getCurrentUser();

        if (empty($user->id)) {
            $this->setAlert('error', 'У вас нет доступа к этой странице.');
            return FALSE;
        }

        $content = $this->ViewRenderer
            ->bindParam('title', '')
            ->bindParam('files', $disk->getUserFileList($user->id))
            ->render('my');

        $diskInfo = $this->ViewRenderer
            ->bindParam('size', $this->formatFileSize($disk->getUserDiskSize($user->id)))
            ->render('disk.info');

        $diskActions = $this->ViewRenderer
            ->render('disk.actions');

        return $this->buildContainer($content, 'disk/my', $diskInfo, $diskActions);
    }

    public function shared()
    {
        $disk = new DiskProvider();

        $content = $this->ViewRenderer
            ->bindParam('files', $disk->getSharedFileList())
            ->render('shared');

        return $this->buildContainer($content, 'disk/shared');
    }

    public function share_files()
    {
        $disk = new DiskProvider();
        $user = (new User)->getCurrentUser();

        $files = $_POST["list"];

        if ($user->hasRole('Кладовщик') || $disk->isFilesReferToUser($files, $user->id)) {
            if ($disk->shareFileById($files)) {
                print 'OK. Выбранным файлам успешно открыт общий доступ';
            } else {
                print 'Возникли проблемы. Пожалуйста, попробуйте позже';
            }
        } else {
            if (empty($user->id)) {
                print 'Пожалуйста, войдите в систему';
            } else {
                print 'У вас нет доступа к этому действию';
            }
        }

        exit();
    }

    public function verify_file($id)
    {
        $disk = new DiskProvider();
        $user = (new User)->getCurrentUser();

        $file = $disk->getFileById($id);

        if ($file === FALSE) {
            $this->setAlert('error', 'Файл с таким ID не найден.');

            if (!empty($user->id)) {
                return $this->feed();
            }
        }

        if ($user->hasRole('Кладовщик')) {
            if ($disk->verifyFileById($id)) {
                print 'OK';
            } else {
                print 'Не могу одобрить файл';
            }
        }

        exit();
    }

    public function download_file($id)
    {
        $disk = new DiskProvider();
        $user = (new User)->getCurrentUser();

        $file = $disk->getFileById($id);

        if ($file === FALSE) {
            $this->setAlert('error', 'Файл с таким ID не найден.');

            if (!empty($user->id)) {
                return $this->my();
            }
        }

        if ($user->hasRole('Кладовщик') || $disk->isFileRefersToUser($file->file_id, $user->id) || $disk->isFileShared($file->file_id)) {
            $filepath = $file->getFilePath();

            @header('Content-type: application/octet-stream');
            @header('Content-Disposition: attachment; filename=' . $file->title);
            @header('Expires: 0');
            @header('Cache-Control: must-revalidate');
            @header('Pragma: public');
            @header('Content-Length: ' . filesize($filepath));

            readfile($filepath);

            exit();
        } else {
            $this->setAlert('error', 'У вас нет доступа к этому файлу.');

            if (!empty($user->id)) {
                return $this->my();
            }
        }
    }

    public function remove_file($id)
    {
        $disk = new DiskProvider();
        $user = (new User)->getCurrentUser();

        $file = $disk->getFileById($id);

        if ($file === FALSE) {
            if (!empty($_POST["ajax"])) {
                print 'Файл с таким ID не найден.';
                exit();
            } else {
                $this->setAlert('error', 'Файл с таким ID не найден.');

                if (!empty($user->id)) {
                    return $this->my();
                }
            }
        }

        if ($user->hasRole('Кладовщик') || $disk->isFileRefersToUser($file->file_id, $user->id)) {
            if ($disk->removeFileById($file->file_id)) {
                @unlink ($file->getFilePath());

                if (!empty($_POST["ajax"])) {
                    print 'OK';
                    exit();
                } else {
                    $this->setAlert('success', 'Файл успешно удален.');

                    return $this->my();
                }
            } else {
                if (!empty($_POST["ajax"])) {
                    print 'Возникли проблемы при удалении файла.';
                    exit();
                } else {
                    $this->setAlert('error', 'Возникли проблемы при удалении файла.');

                    return $this->my();
                }
            }
        } else {
            if (!empty($_POST["ajax"])) {
                if (empty($user->id)) {
                    print 'Пожалуйста, войдите в систему.';
                } else {
                    print 'У вас нет доступа к этому файлу.';
                }

                exit();
            } else {
                if (empty($user->id)) {
                    $this->setAlert('error', 'Пожалуйста, войдите в систему.');
                } else {
                    $this->setAlert('error', 'У вас нет доступа к этому файлу.');

                    return $this->my();
                }
            }
        }
    }

    public function upload()
    {
        $User = new User();

        if (!$User->isUserLoggedIn()) {
            exit('Access denied');
        }

        $User = $User->getCurrentUser();

        $data = json_decode($_POST["data"], TRUE);

        $userId = $User->id;
        $filename = $data["filename"];
        $filesize = $data["filesize"];
        $uniqueId = $data["uniqueId"];

        $title = $filename;
        $filename = str_replace(" ", "_", $filename);
        $filename = explode(".", $filename);
        $format = strtolower(array_pop($filename));
        $filename = implode("", $filename);
        $filename .= "_" . $uniqueId . "." . $format;

        $filepath = APP_PATH . '/public/disk/user' . $userId . '/';

        if (!is_dir($filepath)) {
            mkdir($filepath, 0755);
        }

        $tmp = fopen($_FILES[$uniqueId]["tmp_name"], "rb");
        $file = fopen($filepath . $filename, "ab");

        while ($data = fread($tmp, 4096)) {
            flock($file, LOCK_EX);
            fwrite($file, $data);
            flock($file, LOCK_UN);
        }

        fclose($tmp);
        fclose($file);

        if ($filesize == filesize($filepath . $filename)) {
            $Disk = new DiskProvider();

            if (($file = $Disk->addFile($userId, $title, $format, $filename, $filesize, $_SERVER["REMOTE_ADDR"])) !== FALSE) {
                print $file;
                exit();
            } else {
                @unlink($filepath . $filename);
            }
        }

        exit();
    }

    private function buildContainer($content, $active, $diskInfo = '', $diskActions = '')
    {
        $user = (new User())->getCurrentUser();

        $links = [
            ["url" => "disk/shared", "title" => "Общие файлы", "icon" => '<i class="fa fa-share-square-o fa-fw"></i>']
        ];

        if (!empty($user->id)) {
            array_unshift($links, ["url" => "disk/my", "title" => "Файлы", "icon" => '<i class="fa fa-cloud fa-fw"></i>']);
        }

        if ($user->hasRole('Кладовщик')) {
            array_unshift($links, ["url" => "disk/feed", "title" => "Лента", "icon" => '<i class="fa fa-home fa-fw"></i>']);
        }

        $menu = $this->ViewRenderer
            ->bindParam('links', $links)
            ->bindParam('active', $active)
            ->render('menu');

        return $this->ViewRenderer
            ->bindParam('menu', $menu)
            ->bindParam('content', $content)
            ->bindParam('diskInfo', $diskInfo)
            ->bindParam('diskActions', $diskActions)
            ->render('container');
    }

    private function formatFileSize($size)
    {
        $titles = ["Б", "кБ", "МБ", "ГБ", "ТБ"];

        for ($i = 0, $filesize = $size; $i <= 5 && $filesize >= 1024; ++$i) {
            $filesize /= 1024;
        }

        return round($filesize, 2) . " " . $titles[$i];
    }
} 