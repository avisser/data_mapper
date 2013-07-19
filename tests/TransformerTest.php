<?php

require_once 'TestBase.php';

class TransformerTest extends TestBase
{
    function _getMappings()
    {
        $M1 = new \model\Mapping();
        $M1->label = 'name';
        $M1->formula = 'name';
        $M2 = new \model\Mapping();
        $M2->label = 'latitude';
        $M2->formula = 'geocode/latitude';
        return array($M1, $M2);
    }

    function testBind()
    {

        $Mappings = $this->_getMappings();

        $record = array(
            'name' => 'test name',
            'geocode/latitude' => 'test latitude'
        );

        $result = Transformer::bindRecord($record, $Mappings);
        $expected = array(
            'name' => 'test name',
            'latitude' => 'test latitude'
        );

        $this->assertEquals($expected, $result);
    }

    function testReact()
    {
        $Worksheet = new \model\Worksheet();
        $Worksheet->mappings = $this->_getMappings();
        $Worksheet->record_xpath = '/data/bldg';

        $result = Transformer::react($this->sample('buildings3.xml'), $Worksheet);

        $expected = array(
            array('name' => 'Bldg 1', 'latitude' => '37.789413'),
            array('name' => 'Bldg 2', 'latitude' => '37.789413'),
            array('name' => 'Bldg 3', 'latitude' => '37.789413'),
            array('name' => 'Bldg 4', 'latitude' => '37.789413'),
        );

        $this->assertEquals($expected, $result);
    }
}