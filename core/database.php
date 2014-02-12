<?php
namespace Core;

class Database extends \PDO
{
    public function __construct()
    {
        try {
            parent::__construct("mysql:host=localhost;dbname=prolounge", "prolounge", "proloungelocal");

            $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            print "PDO: " . $e->getMessage();
        }
    }

    public function getRows($sth)
    {
        return $sth->fetchAll();
    }

    public function getRow($sth)
    {
        return $sth->fetch();
    }

    public function rawQuery($statement)
    {
        return $this->exec($statement);
    }
}
