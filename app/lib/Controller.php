<?php
namespace App\Lib;

use Core\Services;
use Library\User;
use Utils\Library\QueueModel;

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
        $User = new User();

        return $this->ViewRenderer
            ->bindParam('categories', $this->lib->getCategoryList())
            ->bindParam('user', $User->getCurrentUser())
            ->render('index');
    }

    /**
     * Отображение детальной информации о книге $id
     * @param $id
     */
    public function book($id)
    {
        $User = new User();
        return $this->ViewRenderer
            ->bindParam('book', $this->lib->findBookById($id))
            ->bindParam('user', $User->getCurrentUser())
            ->render('book');
    }

    /**
     * Отображение книг в выбранной категории $id
     * @param $id
     */
    public function category($id)
    {
        $User = new User();

        return $this->ViewRenderer
            ->bindParam('user', $User->getCurrentUser())
            ->bindParam('category', $this->lib->findCategoryById($id))
            ->bindParam('books', $this->lib->findBooksByCategoryId($id))
            ->render('category');
    }

    /**
     * Форма добавления книги в библиотеку
     */
    public function addbook()
    {
        if (!empty($_POST)) {
            $_POST["ebook"] = !empty($_POST["ebook"]) ? 1 : 0;
            $_POST["book"] = $_POST["ebook"] ? $_FILES["book"]["name"] : "";
            $_POST["image"] = $_FILES['image']['name'];

            if (count($this->validate($_POST)) > 0) {
                $this->setAlert('error', "Пожалуйста, заполните все необходимые поля.");
            } else {
                $imagefile = time() . '_' . $_FILES['image']['name'];

                if (move_uploaded_file($_FILES['image']['tmp_name'], APP_PATH . '/public/books/images/' . $imagefile)) {
                    $_POST["image"] = $imagefile;

                    // Загрузка книги, если нужно
                    if ($_POST["ebook"]) {
                        $book_parts = explode('.', $_FILES["book"]["name"]);
                        $bookext = array_pop($book_parts);
                        $bookfile = time() . '_' . mt_rand(0, 50) . '.' . $bookext;

                        if (move_uploaded_file($_FILES["book"]["tmp_name"], APP_PATH . '/public/books/' . $bookfile)) {
                            $_POST["book"] = $bookfile;
                        } else {
                            $this->setAlert('error', 'При загрузке файла книги произошла ошибка.');
                        }
                    } else {
                        $_POST["book"] = "";
                    }

                    if (($id = $this->lib->addBook($_POST)) !== false) {
                        $this->setAlert('success', 'Книга успешно добавлена!');
                    } else {
                        $this->setAlert('error', "Видимо, какие-то проблемы с базой данных.");
                    }
                } else {
                    $this->setAlert('error', 'Возникла ошибка при загрузке картинки. ');
                }
            }
        }

        return $this->ViewRenderer
            ->bindParam('categories', $this->lib->getCategoryList())
            ->render('addbook');
    }

    /**
     * Форма добавления раздела в библиотеку
     */
    public function addcategory()
    {
        if (!empty($_POST)) {
            if (count($this->validate($_POST)) > 0) {
                $this->displayAlertError("Пожалуйста, заполните все необходимые поля.");
            }
        }

        return $this->ViewRenderer->render('addcategory');
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

        $image_extensions = array('jpg', 'jpeg');
        $book_extensions = array('pdf', 'djvu');

        $params = !empty($_POST["params"]) ? json_decode($_POST["params"], true) : $params;
        $extra = !empty($_POST["extra"]) ? json_decode($_POST["extra"], true) : $params;

        if (empty($params)) {
            return false;
        }

        $result = array();

        // Если установлен флаг "Электронная книга", то должен быть выбран файл этой книги
        if (!empty($extra["ebook"])) {
            $ext = explode(".", $params["book"]);
            $ext = strtolower(array_pop($ext));

            if (strlen($params["book"]) == 0) {
                $result[] = "book";
            } elseif (!in_array($ext, $book_extensions)) {
                $result[] = "book";
            }
        }

        $image_ext = explode(".", $params["image"]);
        $image_ext = strtolower(array_pop($image_ext));

        if (!in_array($image_ext, $image_extensions)) {
            $result[] = "image";
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

    public function add_queue($book_id, $user_id, $is_owner = 0)
    {
        $queue = new QueueModel();

        if ($queue->addToQueue($book_id, $user_id, $is_owner)) {
            $this->displayAlertSuccess("Вы успешно добавлены в очередь на книгу. <a href='/lib/book/$book_id'>Вернуться назад</a>");
        } else {
            $this->displayAlertError('Возникла ошибка. Возможно, проблемы с БД.');
        }

        return;
    }

    public function remove_queue($book_id, $user_id)
    {
        $queue = new QueueModel();

        if ($queue->removeFromQueue($book_id, $user_id)) {
            $this->displayAlertSuccess("Вы успешно вышли из очереди на книгу. <a href='/lib/book/$book_id'>Вернуться назад</a>");
        } else {
            $this->displayAlertError('Возникла ошибка. Возможно, проблемы с БД.');
        }

        return;
    }

    /**
     * Удаление категории с всех книг в ней.
     * С подтверждением от пользователя.
     * @param $id
     * @return string
     */
    public function remove_category($id)
    {
        $user = new User();
        $user = $user->getCurrentUser();

        if (!$user->hasRole('Администратор')) {
            $this->setAlert('error', 'У Вас нет привилегий на это.');
            return $this->category($id);
        }

        $category = $this->lib->findCategoryById($id);

        if (!isset($_REQUEST["confirm"])) {
            $content = $this->ViewRenderer
                ->bindParam('category', $category)
                ->render('category_remove');
        } else {
            if ($this->lib->removeCategoryById($id)) {
                $content = $this->ViewRenderer
                    ->bindParam('category', $category)
                    ->render('category_removed');
            } else {
                $this->setAlert('error', 'Невозможно удалить категорию.');

                $content = $this->ViewRenderer
                    ->bindParam('category', $category)
                    ->render('category_remove');
            }
        }

        return $content;
    }

    /**
     * Удаление книги с подтверждением
     * @param $id
     * @return string
     */
    public function remove_book($id)
    {
        $user = new User();
        $user = $user->getCurrentUser();

        if (!$user->hasRole('Администратор')) {
            $this->setAlert('error', 'У Вас нет привилегий на это.');
            return $this->book($id);
        }

        $book = $this->lib->findBookById($id);

        if (!isset($_REQUEST["confirm"])) {
            $content = $this->ViewRenderer
                ->bindParam('book', $book)
                ->render('book_remove');
        } else {
            if ($this->lib->removeBookById($id)) {
                $content = $this->ViewRenderer
                    ->bindParam('book', $book)
                    ->render('book_removed');
            } else {
                $this->setAlert('error', 'Невозможно удалить книгу.');

                $content = $this->ViewRenderer
                    ->bindParam('book', $book)
                    ->render('book_remove');
            }
        }

        return $content;
    }

    /**
     * Форма изменения раздела
     * @param $id
     */
    public function edit_category($id)
    {
        if (!empty($_POST)) {
            if (count($this->validate($_POST)) == 0) {
                if ($this->lib->updateCategoryById($id, $_POST)) {
                    $this->setAlert('success', 'Информация о разделе успешно обновлена.');

                    return $this->category($id);
                } else {
                    $this->setAlert('error', 'Возникли проблемы при обновлении раздела.');
                }
            } else {
                $this->setAlert('error', 'Неверно заполнены поля формы.');
            }
        }

        return $this->ViewRenderer
            ->bindParam('category', $this->lib->findCategoryById($id))
            ->render('category_edit');
    }

    /**
     * Форма изменения книги
     * @param $id
     * @return string
     */
    public function edit_book($id)
    {
        if (!empty($_POST)) {
            if (count($this->validate($_POST)) == 0) {
                if ($this->lib->updateBookById($id, $_POST)) {
                    $this->setAlert('success', 'Книга успешно обновлена!');

                    return $this->book($id);
                } else {
                    $this->setAlert('error', "Видимо, какие-то проблемы с базой данных.");
                }
            } else {
                $this->setAlert('error', 'Неверно заполнены поля формы.');
            }
        }

        return $this->ViewRenderer
            ->bindParam('categories', $this->lib->getCategoryList())
            ->bindParam('book', $this->lib->findBookById($id))
            ->render('book_edit');
    }
}
