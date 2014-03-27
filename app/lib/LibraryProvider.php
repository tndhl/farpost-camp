<?php
namespace App\Lib;

use Core\Database\Provider;

class LibraryProvider extends Provider
{
    public function getCategoryList()
    {
        $sth = $this->prepare(
            "SELECT id, title
            FROM lib_category
            ORDER BY title"
        );

        $sth->execute();
        return $sth->fetchAll();
    }

    public function findCategoryById($id)
    {
        $sth = $this->prepare(
            "SELECT id, title
            FROM lib_category
            WHERE id = ?"
        );

        $sth->execute(array($id));
        return $sth->fetch();
    }

    public function findBooksByCategoryId($id)
    {
        $sth = $this->prepare(
            "SELECT id, category, title, author, annotation, publisher, image, is_ebook, ebook_file
            FROM lib_book
            WHERE category = ?"
        );

        $sth->execute(array($id));
        return $sth->fetchAll(\PDO::FETCH_CLASS, '\Utils\Library\BookEntity');
    }

    public function addBook($params)
    {
        $sth = $this->prepare(
            "INSERT INTO lib_book (category, title, author, annotation, publisher, image, is_ebook, ebook_file)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );

        if ($sth->execute(
            array(
                $params["category"],
                $params["title"],
                $params["author"],
                $params["annotation"],
                $params["publisher"],
                $params["image"],
                $params["ebook"],
                $params["book"]
            ))) {
            return $this->lastInsertId();
        } 

        return false;
    }

    public function findBookById($id)
    {
        $sth = $this->prepare(
            "SELECT id, category, title, author, annotation, publisher, image, is_ebook, ebook_file
            FROM lib_book
            WHERE id = ?"
        );

        $sth->execute(array($id));
        $sth->setFetchMode(\PDO::FETCH_CLASS, '\Utils\Library\BookEntity');

        return $sth->fetch();
    }
}
