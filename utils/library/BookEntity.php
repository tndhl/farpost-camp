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

    /**
     * Проверка, на руках ли книга
     *
     * @return bool
     */
    public function isOwned()
    {
        $queue = new QueueModel();

        return $queue->isBookOwned($this->id);
    }

    /**
     * Текущий статус книги. (Книга свободна|На руках)
     *
     * @return string
     */
    public function getQueueStatus()
    {
        $queue = new QueueModel();

        return $queue->getBookStatus($this->id);
    }

    /**
     * Текущий владелец книги
     *
     * @return string
     */
    public function getCurrentOwner()
    {
        $queue = new QueueModel();

        return $queue->getBookOwnerName($this->id);
    }

    /**
     * Дата взятия книги
     *
     * @return string
     */
    public function getTakingDate()
    {
        $queue = new QueueModel();

        return $queue->getBookTakingDate($this->id);
    }

    /**
     * Длина очереди на книгу
     *
     * @return string
     */
    public function getQueueLength()
    {
        $queue = new QueueModel();

        return $queue->getBookQueueLength($this->id);
    }
}
