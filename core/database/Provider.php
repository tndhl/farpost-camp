<?php
namespace Core\Database;

use PDO;
use PDOException;

class Provider extends PDO
{
    public function __construct()
    {
        try {
            parent::__construct("mysql:host=localhost;dbname=farpost", "farpost", "farpostlocal", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            print "PDO: " . $e->getMessage();
        }
    }
}
