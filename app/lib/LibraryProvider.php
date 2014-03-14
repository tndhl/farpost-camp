<?php
namespace App\Lib;

class LibraryProvider extends \Core\Database\Provider
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
            "SELECT id, category, title, author, annotation, publisher, image
            FROM lib_book
            WHERE category = ?"
        );

        $sth->execute(array($id));
        return $sth->fetchAll(\PDO::FETCH_CLASS, "\App\Lib\Assets\Book");
    }
}
