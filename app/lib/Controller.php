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

    public function addbook()
    {
        $content = $this->ViewRenderer->bindParam('categories', $this->lib->getCategoryList())->render('addbook');

        if (!empty($_POST)) {
            if (count($this->addbook_validate($_POST)) > 0) {
                $this->displayAlertError("Пожалуйста, заполните все необходимые поля.");
            } else {
                $imagefile = time() . '_' . $_FILES['image']['name'];

                if (@move_uploaded_file($_FILES['image']['tmp_name'], APP_PATH . '/public/images/books/' . $imagefile)) {
                    $_POST["image"] = $imagefile;

                    if (($id = $this->lib->addBook($_POST)) !== false) {
                        $this->displayAlertSuccess('Книга успешно добавлена!');
                    } else {
                        $this->displayAlertError("Видимо, какие-то проблемы с базой данных.");
                    }
                } else {
                    $this->displayAlertError('Возникла ошибка при загрузке картинки. ' . $_FILES['image']['error']);
                }
            }
        }

        $this->LayoutRenderer->bindParam('content', $content)->render();
    }

    public function addbook_validate($params = array())
    {
        $required_params = array(
            "title",
            "image",
            "author",
            "category",
            "publisher"
        );

        $params = !empty($_POST["params"]) ? json_decode($_POST["params"], true) : $params;

        if (empty($params)) {
            return false;
        }

        $result = array();

        foreach ($params as $attribute => $value) {
            if (in_array($attribute, $required_params)) {
                if (strlen($value) == 0) {
                    $result[] = $attribute;
                }
            }
        }

        if (empty($_POST["params"])) {
            return $result;
        } else {
            echo json_encode($result);
            exit;
        }
    }

    public function addcategory()
    {
        $content = $this->ViewRenderer->render('addbook');

        if (!empty($_POST)) {
            if (count($this->addcategory_validate($_POST)) > 0) {
                $this->displayError("Пожалуйста, заполните все необходимые поля.");
            }
        }

        $this->LayoutRenderer->bindParam('content', $content)->render();
    }

    public function addcategory_validate($params = array())
    {
        $required_params = array(
            "title"
        );

        $params = !empty($_POST["params"]) ? json_decode($_POST["params"], true) : $params;

        if (empty($params)) {
            return false;
        }

        $result = array();
        
        foreach ($params as $attribute => $value) {
            if (in_array($attribute, $required_params)) {
                if (strlen($value) == 0) {
                    $result[] = $attribute;
                }
            }
        }

        if (empty($_POST["params"])) {
            return $result;
        } else {
            echo json_encode($result);
            exit;
        }
    }
}
