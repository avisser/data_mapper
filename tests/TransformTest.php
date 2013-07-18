<?php

require_once 'TestBase.php';

class TransformTest extends TestBase
{
    function testFindHighCardinalityNode()
    {
        $transform = Transform();
        $output = $transform->getRecordNode('../sample_data/photos_rss.xml');
        $this->assertEquals('/rss/channel/item', $output);
    }

}