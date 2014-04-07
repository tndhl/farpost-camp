<?php
namespace Utils\Library;


class BookEntity
{
    public $id;
    public $category;
    public $title;
    public $author;
    public $annotation;
    public $publisher;
    public $image;
    public $is_ebook;
    public $ebook_file;

    public function isOwned()
    {
        $queue = new QueueModel();

        return $queue->isBookOwned($this->id);
    }

    public function getQueueStatus()
    {
        $queue = new QueueModel();

        return $queue->getBookStatus($this->id);
    }

    public function getCurrentOwner()
    {
        $queue = new QueueModel();

        return $queue->getBookOwnerName($this->id);
    }

    public function getTakingDate()
    {
        $queue = new QueueModel();

        return $queue->getBookTakingDate($this->id);
    }

    public function getQueueLength()
    {
        $queue = new QueueModel();

        return $queue->getBookQueueLength($this->id);
    }
}
