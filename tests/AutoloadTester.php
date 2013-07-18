<?php

require_once './TestBase.php';

class AutoloadTester extends TestBase
{
    function testFoo()
    {
        $foo = new Foo();
        $this->assertTrue(true); //got here!
    }
}