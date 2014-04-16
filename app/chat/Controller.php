<?php
namespace App\Chat;

use Core\Services;
use Library\User;

class Controller extends Services
{
    /**
     * @AJAX
     *
     * Возвращает новые сообщения для пользователя, если они есть
     */
    public function getMessageList()
    {
        $chat = new ChatProvider();

        if (!empty($_POST["ajax"])) {
            $timeout = 360;
            $time = time();

            $last_msg_id = $_POST["last_msg_id"];

            set_time_limit($timeout + 5);

            while (time() - $time < $timeout) {
                if ($chat->hasNewMessages($last_msg_id)) {
                    $messages = $chat->getMessageList($last_msg_id);

                    echo json_encode($messages);

                    flush();
                    exit();
                }

                sleep(5);
            }
        }
    }

    /**
     * @AJAX
     *
     * Добавляет новое сообщение в базу или возвращает ошибку
     */
    public function sendMessage()
    {
        $chat = new ChatProvider();
        $user = (new User())->getCurrentUser();

        $message = $_POST["message"];

        if (!empty($_POST["ajax"])) {
            if (empty($message)) {
                echo json_encode(["error" => "Сообщение должно быть не пустым"]);
                exit();
            }

            if (empty($user->id)) {
                echo json_encode(["error" => "Пожалуйста, войдите в систему"]);
                exit();
            }

            if (($id = $chat->addMessage($user->id, $message, $_SERVER["REMOTE_ADDR"]))) {
                echo json_encode($chat->getMessageById($id));
                exit();
            } else {
                echo json_encode(["error" => "Возникли ошибки при добавлении сообщения"]);
                exit();
            }
        }
    }
} 