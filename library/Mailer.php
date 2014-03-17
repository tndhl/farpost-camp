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
     * Set title of sender
     * @param string $sender 
     */
    public function setSenderTitle($sender)
    {
        $this->sender = $sender;
    }

    /**
     * Set email to reply
     * @param string $email 
     */
    public function setReplyEmail($email)
    {
        $this->replyEmail = $email;
    }

    /**
     * Set mail receiver
     * @param $receiver
     * @internal param string $to
     */
    public function setReceiver($receiver)
    {
        $this->receiver = $receiver;
    }

    /**
     * Set email subject
     * @param string $subject 
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Set email text
     * @param string $message 
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Send email to $receiver
     * @return boolean
     */
    public function sendEmail()
    {
        if (empty($this->receiver) || empty($this->subject) || empty($this->message)) {
            return false;
        }

        $headers = "From: =?utf-8?b?" . base64_encode($this->sender) . "?= <" . $this->replyEmail . ">\r\n";
        $headers .= "Content-Type: text/plain;charset=utf-8\r\n";

        $this->subject = "=?utf-8?b?" . base64_encode($this->subject) . "?=";

        if (mail($this->receiver, $this->subject, $this->message, $headers)) {
            return true;
        }

        return false;
    }    
}
