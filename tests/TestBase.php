<?php

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../application/app.php';

class TestBase extends PHPUnit_Framework_TestCase
{

    /**
     * @param $file
     * @return string
     */
    public function sample($file)
    {
        return __DIR__."/../sample_data/$file";
    }
}