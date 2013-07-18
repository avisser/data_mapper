<?php

require_once 'TestBase.php';

class TransformTest extends TestBase
{
    function testFindHighCardinalityNode()
    {
        $transform = new Transformer();
        $samples = array(
            'buildings1.xml' => '/data/bldg',
            'buildings2.xml' => '/data/bldg',
            'buildings3.xml' => '/data/bldg',
            'buildings4.xml' => '/data/bldg',
            'input2.xml' => '/data/MAScore',
            '/photos_rss.xml' => '/rss/channel/item');

        foreach ($samples as $sample => $xpath)
        {
            $output = $transform->getRecordNode($this->sample($sample));
            $this->assertEquals($xpath, $output);
        }
    }


}