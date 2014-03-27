<?php
namespace App\Lib;

use Core\Services;

class Controller extends Services
{
    public function __construct()
    {
        parent::__construct();
        $this->lib = new LibraryProvider();
    }

    /**
     * Главная страница библиотеки
     */
    public function index()
    {
        $content = $this->ViewRenderer
            ->bindParam('categories', $this->lib->getCategoryList())
            ->render('index');

        $this->LayoutRenderer->bindParam('content', $content)->render();
    }

    /**
     * Отображение детальной информации о книге $id
     * @param $id
     */
    public function book($id)
    {
        $content = $this->ViewRenderer
            ->bindParam('book', $this->lib->findBookById($id))
            ->render('book');

        $this->LayoutRenderer->bindParam('content', $content)->render();
    }

    /**
     * Отображение книг в выбранной категории $id
     * @param $id
     */
    public function category($id)
    {
        $content = $this->ViewRenderer
            ->bindParam('category', $this->lib->findCategoryById($id))
            ->bindParam('books', $this->lib->findBooksByCategoryId($id))
            ->render('category');

        $this->LayoutRenderer->bindParam('content', $content)->render();
    }

    /**
     * Форма добавления книги в библиотеку
     */
    public function addbook()
    {
        $content = $this->ViewRenderer->bindParam('categories', $this->lib->getCategoryList())->render('addbook');

        if (!empty($_POST)) {
            $_POST["ebook"] = !empty($_POST["ebook"]) ? 1 : 0;

            if (count($this->validate($_POST)) > 0) {
                $this->displayAlertError("Пожалуйста, заполните все необходимые поля.");
            } else {
                $imagefile = time() . '_' . $_FILES['image']['name'];

                if (move_uploaded_file($_FILES['image']['tmp_name'], APP_PATH . '/public/images/books/' . $imagefile)) {
                    $_POST["image"] = $imagefile;

                    // Загрузка книги, если нужно
                    if ($_POST["ebook"]) {
                        $bookext = end(explode('.', $_FILES["book"]["name"]));
                        $bookfile = time() . '_' . mt_rand(0, 50) . '.' . $bookext;

                        if (move_uploaded_file($_FILES["book"]["tmp_name"], APP_PATH . '/public/books/' . $bookfile)) {
                            $_POST["book"] = $bookfile;
                        } else {
                            $this->displayAlertError('При загрузке файла книги произошла ошибка.');
                        }
                    } else {
                        $_POST["book"] = "";
                    }

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

    /**
     * Форма добавления раздела в библиотеку
     */
    public function addcategory()
    {
        $content = $this->ViewRenderer->render('addcategory');

        if (!empty($_POST)) {
            if (count($this->validate($_POST)) > 0) {
                $this->displayAlertError("Пожалуйста, заполните все необходимые поля.");
            }
        }

        $this->LayoutRenderer->bindParam('content', $content)->render();
    }

    /**
     * Проверка отправленных данных.
     * В т.ч. через AJAX. (form.validation.js)
     * @param array $params
     * @return array|bool
     */
    public function validate($params = array())
    {
        // Массив обязательных для заполнения полей формы
        $required_params = array(
            "title",
            "image",
            "author",
            "category",
            "publisher"
        );

        $params = !empty($_POST["params"]) ? json_decode($_POST["params"], true) : $params;
        $extra = !empty($_POST["extra"]) ? json_decode($_POST["extra"], true) : $params;

        if (empty($params)) {
            return false;
        }

        $result = array();

        // Если установлен флаг "Электронная книга", то должен быть выбран файл этой книги
        if ($extra["ebook"]) {
            if (strlen($params["book"]) == 0) {
                $result[] = "book";
            }
        }

        // Простая проверка на длинну > 0
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
