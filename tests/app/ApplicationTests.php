<?php
namespace tests\app;

use PHPUnit_Framework_TestSuite;

class ApplicationTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Application Tests');

        // $suite->addTestSuite('ClassTest');

        return $suite;
    }
} 