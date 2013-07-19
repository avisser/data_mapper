<?php

require_once 'TestBase.php';

class IndexTest extends TestBase
{
    function testWholeBag()
    {
        $tmp_file = $this->sample('buildings1.xml');
        $processor = new PreProcessor();
        $processor->setContentsFromFilename($tmp_file);
        $xpath = $processor->getRecordXPath();
        $record_schema = $processor->getRecordSchema($xpath);

        $this->assertTrue(true);
    }
}