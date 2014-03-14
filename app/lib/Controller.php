<?php
namespace App\Lib;

class Controller extends \Core\Services
{
    public function __construct()
    {
        parent::__construct();
        $this->lib = new LibraryProvider();
    }

    public function index()
    {
        $content = $this->ViewRenderer
            ->bindParam('categories', $this->lib->getCategoryList())
            ->render('index');

        $this->LayoutRenderer->bindParam('content', $content)->render();
    }

    public function category($id)
    {
        $content = $this->ViewRenderer
            ->bindParam('category', $this->lib->findCategoryById($id))
            ->bindParam('books', $this->lib->findBooksByCategoryId($id))
            ->render('category');

        $this->LayoutRenderer->bindParam('content', $content)->render();
    }
}
