<?php
namespace Core;

class Services
{
    protected $services = array();

    public function __construct()
    {
        $this->template = new \Core\Template(get_class($this));

        $user = new \Library\User;

        if ($user->isUserLoggedIn()) {
            $this->template->bindParam("user", $this->template->getHtml("templates.user_logged", array("profile" => $user->getUserData())));
        } else {
            $this->template->bindParam("user", $this->template->getHtml("templates.user_links"));
        }

        if (isset($_GET["logout"])) {
            $user->userLogout();
            $this->redirect("/");
        }
    }

    public function redirect($url)
    {
        @header("Location: " . $url);
        exit;
    }

    public function recursive_array_search($needle, &$haystack)
    {
        foreach ($haystack as $key => $value) {
            if ($needle === $value || (is_array($value) && $this->recursive_array_search($needle, $value) !== false)) {
                return array("key" => $key, "param" => $value);
            }
        }

        return false;
    }
    
    public function __set($key, $value)
    {
        $this->services[$key] = $value;
    }

    public function __get($key)
    {
        if (isset($this->services[$key])) {
            return $this->services[$key];
        }
    }
}
