<?php

require_once './TestBase.php';

class AutoloadTester extends TestBase
{
    function testFoo()
    {
        $foo = new Foo();
        $this->assertNotNull($foo); //got here!
    }

    function testNS()
    {
        $foo = new model\Mapping();
        $this->assertNotNull($foo); //got here!
    }
}