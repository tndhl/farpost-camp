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

    public function getEbookFilePath()
    {
        return '/public/books/' . $this->ebook_file;
    }
}
