<?php

require_once '../TestBase.php';

class GeneratorTest extends TestBase
{
    function testExtractFieldMap()
    {
        $gen = new FieldMap\Generator($this->sample('buildings1.xml'));
        $schema = $gen->getSchemaForXPath('/data/bldg');
        foreach( array('name', 'lat', 'lon') as $field )
        {
            $this->assertContains($field, $schema);
        }
    }

    function testExtractFieldMapNested()
    {
        $gen = new FieldMap\Generator($this->sample('buildings3.xml'));
        $schema = $gen->getSchemaForXPath('/data/bldg');
        foreach( array('name', 'geocode/latitude', 'geocode/longitude') as $field )
        {
            $this->assertContains($field, $schema);
        }
    }
}