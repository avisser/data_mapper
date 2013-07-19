<?php

require_once 'TestBase.php';

class SchemaTest extends TestBase
{
    function testSchema()
    {
        $schema = model\Schema::load("photos");
        print_r($schema);
    }
}