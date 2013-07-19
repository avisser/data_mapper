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

    /**
     * Returns true iff the two arrays have
     * - the same number of keys
     * - the same keys
     * Ignores order
     * @param $expected array
     * @param $actual array
     */
    public function assertEquivalent($expected, $actual)
    {
        $this->assertEquals( count($expected), count($actual), "Arrays are of differing length");

        foreach ($expected as $e)
        {
            $this->assertContains($e, $actual);
        }

        foreach ($actual as $a)
        {
            $this->assertContains($a, $expected);
        }
    }
}