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
            ))
        ) {
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

    public function removeCategoryById($id)
    {
        $sth = $this->prepare(
            "DELETE lib_book, lib_category
            FROM lib_category
            LEFT JOIN lib_book ON lib_book.category = lib_category.id
            WHERE lib_category.id = ?"
        );

        if ($sth->execute(array($id))) {
            return true;
        }

        return false;
    }

    public function removeBookById($id)
    {
        $sth = $this->prepare(
            "DELETE lib_book, lib_queue
            FROM lib_book
            LEFT JOIN lib_queue ON lib_book.id = lib_queue.book_id
            WHERE lib_book.id = ?"
        );

        if ($sth->execute(array($id))) {
            return true;
        }

        return false;
    }
}
