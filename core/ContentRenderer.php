<?php
namespace Core;

use Library\User;

class ContentRenderer
{
    private $content;
    private $alert;

    /**
     * Обработка данных контроллера.
     * В случае AJAX-запроса вернет только их.
     */
    public function render()
    {
        if (empty($_POST["ajax_page_load"])) {
            $LayoutRenderer = new LayoutRenderer;
            $user = new User;

            if ($user->isUserLoggedIn()) {
                $LayoutRenderer
                    ->bindParam("user", $user->getCurrentUser())
                    ->bindParam("userlinks", $LayoutRenderer->render('user_logged', false));
            } else {
                $LayoutRenderer
                    ->bindParam('userlinks', $LayoutRenderer->render('user_links', false))
                    ->bindParam('user', $user->getCurrentUser());
            }

            $LayoutRenderer
                ->bindParam('content', $this->content)
                ->bindParam('alert', $this->alert)
                ->render();
        } else {
            echo $this->content;
            exit;
        }
    }

    public function setAlert($alert = '')
    {
        $this->alert = $alert;
    }

    public function setContent($content = '')
    {
        $this->content = $content;
    }
} 