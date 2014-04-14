<?php
namespace Library;

class Mailer
{
    private $receiver;
    private $subject;
    private $message;
    private $sender = "FarPost Portal";
    private $replyEmail = "noreply@farpostportal";

    /**
     * Установка имени отправителя
     *
     * @param string $sender
     */
    public function setSenderTitle($sender)
    {
        $this->sender = $sender;
    }

    /**
     * Установка Email для ответа
     *
     * @param string $email
     */
    public function setReplyEmail($email)
    {
        $this->replyEmail = $email;
    }

    /**
     * Установка Email получателя
     *
     * @param string $receiver E-mail
     */
    public function setReceiver($receiver)
    {
        $this->receiver = $receiver;
    }

    /**
     * Установка темы
     *
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Установка тела
     *
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Отправка
     *
     * @return boolean
     */
    public function sendEmail()
    {
        if (empty($this->receiver) || empty($this->subject) || empty($this->message)) {
            return FALSE;
        }

        $headers = "From: =?utf-8?b?" . base64_encode($this->sender) . "?= <" . $this->replyEmail . ">\r\n";
        $headers .= "Content-Type: text/plain;charset=utf-8\r\n";

        $this->subject = "=?utf-8?b?" . base64_encode($this->subject) . "?=";

        if (mail($this->receiver, $this->subject, $this->message, $headers)) {
            return TRUE;
        }

        return FALSE;
    }
}
