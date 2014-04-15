<?php
namespace Utils\Disk;

class FileEntity
{
    public $file_id;
    public $user_id;
    public $title;
    public $format;
    public $created;
    public $filename;
    public $filesize;
    public $shared;
    public $verified = 0;

    public function formatCreatedDate()
    {
        return date("d/m/Y H:s", strtotime($this->created));
    }

    public function formatFileSize()
    {
        $titles = ["Б", "кБ", "МБ", "ГБ", "ТБ"];

        for ($i = 0, $size = $this->filesize; $i <= 5 && $size >= 1024; $i++) {
            $size /= 1024;
        }

        return round($size, 2) . " " . $titles[$i];
    }

    public function getFilePath()
    {
        return APP_PATH . '/public/disk/user' . $this->user_id . '/' . $this->filename;
    }

    function __toString()
    {
        $params = [
            "file_id" => $this->file_id,
            "user_id" => $this->user_id,
            "title" => $this->title,
            "format" => $this->format,
            "created" => $this->formatCreatedDate(),
            "filename" => $this->filename,
            "filesize" => $this->formatFileSize(),
            "shared" => $this->shared
        ];

        return json_encode($params);
    }


} 