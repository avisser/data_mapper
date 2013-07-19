<?php

require_once 'TestBase.php';

class TransformTest extends TestBase
{
    function testFindHighCardinalityNode()
    {
        $PreProcessor = new PreProcessor();
        $samples = array(
            'buildings1.xml' => '/data/bldg',
            'buildings2.xml' => '/data/bldg',
            'buildings3.xml' => '/data/bldg',
            'buildings4.xml' => '/data/bldg',
            'input2.xml' => '/data/MAScore',
            '/photos_rss.xml' => '/rss/channel/item');

        foreach ($samples as $sample => $xpath)
        {
            $output = $PreProcessor->getRecordXPath($this->sample($sample));
            $this->assertEquals($xpath, $output);
            $PreProcessor->killCache();
        }
    }

    function testGetRecordSchemaBldgs1()
    {
        $transform = new PreProcessor();
        $XPATH = <<<XPATH
/data/bldg/name='Bldg 1'
/data/bldg/lat='37.789413'
/data/bldg/lon='-122.425827'
/data/bldg[2]/name='Bldg 2'
/data/bldg[2]/lat='37.789413'
/data/bldg[2]/lon='-122.425827'
/data/bldg[3]/name='Bldg 3'
/data/bldg[3]/lat='37.789413'
/data/bldg[3]/lon='-122.425827'
/data/bldg[4]/name='Bldg 4'
/data/bldg[4]/lat='37.789413'
/data/bldg[4]/lon='-122.425827'
XPATH;

        $XPATH = explode("\n", $XPATH);
        $prefix = '/data/bldg';
        $schema = $transform->getRecordSchema($XPATH, $prefix);
        $expected = array('name', 'lat', 'lon');
        foreach ($expected as $field)
        {
            $this->assertContains($field, $schema);
        }
    }

    function testGetRecordSchemaPhotos()
    {
        $PreProcessor = new PreProcessor();
        $XPATH = file_get_contents($this->sample('photos.txt'));

        $XPATH = explode("\n", $XPATH);
        $prefix = '/rss/channel/item';
        $schema = $PreProcessor->getRecordSchema($XPATH, $prefix);
        $expected = array('title', 'link', 'description', 'pubDate', 'dc:date.Taken', 'author', 'guid', 'media:content', 'media:title', 'media:credit', 'media:thumbnail');
        foreach ($expected as $field)
        {
            $this->assertContains($field, $schema);
        }
    }

    function testFoo()
    {
        $PreProcessor = new PreProcessor();
        $XPATH = file_get_contents($this->sample('bldgs3.txt'));

        $XPATH = explode("\n", $XPATH);
        $prefix = '/data/bldg';
        $schema = $PreProcessor->getRecordSchema($XPATH, $prefix);
        $expected = array('name' , 'geocode/latitude', 'geocode/longitude');
        foreach ($expected as $field)
        {
            $this->assertContains($field, $schema);
        }
    }

    function testExtract()
    {
        $PreProcessor = new PreProcessor();
        $content = file_get_contents($this->sample('buildings1.xml'));
        $results = $PreProcessor->getRecords($this->sample('buildings1.xml'), '/data/bldg');

        $expected = array(
            array('name' => 'Bldg 1', 'lat' => '37.789413', 'lon' => '-122.425827'),
            array('name' => 'Bldg 2', 'lat' => '37.789413', 'lon' => '-122.425827'),
            array('name' => 'Bldg 3', 'lat' => '37.789413', 'lon' => '-122.425827'),
            array('name' => 'Bldg 4', 'lat' => '37.789413', 'lon' => '-122.425827')
        );

        $this->assertEquals($expected, $results);
    }
}