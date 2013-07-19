<?php

require_once 'TestBase.php';

class PreProcessorTest extends TestBase
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
        $schema = $transform->getRecordSchema($prefix, $XPATH);
        $expected = array('name', 'lat', 'lon');
        $this->assertEquals($expected, $schema);
    }

    function testGetRecordSchemaPhotos()
    {
        $PreProcessor = new PreProcessor();
        $XPATH = file_get_contents($this->sample('photos.txt'));

        $XPATH = explode("\n", $XPATH);
        $prefix = '/rss/channel/item';
        $schema = $PreProcessor->getRecordSchema($prefix, $XPATH);
        $expected = array('title', 'link', 'description', 'pubDate', 'dc:date.Taken', 'author', 'guid', 'media:content', 'media:title', 'media:credit', 'media:thumbnail', 'media:category');
        $this->assertEquivalent($expected, $schema);
    }

    function testFoo()
    {
        $PreProcessor = new PreProcessor();
        $XPATH = file_get_contents($this->sample('bldgs3.txt'));

        $XPATH = explode("\n", $XPATH);
        $prefix = '/data/bldg';
        $schema = $PreProcessor->getRecordSchema($prefix, $XPATH);
        $expected = array('name' , 'geocode/latitude', 'geocode/longitude');
        $this->assertEquals($expected, $schema);
    }

    function testGetRecords()
    {
        $PreProcessor = new PreProcessor();
        $results = $PreProcessor->getRecords($this->sample('buildings1.xml'), '/data/bldg');

        $expected = array(
            array('name' => 'Bldg 1', 'lat' => '37.789413', 'lon' => '-122.425827'),
            array('name' => 'Bldg 2', 'lat' => '37.789413', 'lon' => '-122.425827'),
            array('name' => 'Bldg 3', 'lat' => '37.789413', 'lon' => '-122.425827'),
            array('name' => 'Bldg 4', 'lat' => '37.789413', 'lon' => '-122.425827')
        );

        $this->assertEquals($expected, $results);
    }

    function testAttributes()
    {
        $xsl_output = <<<XSL
/rss/channel/item/pubDate='Thu, 11 Apr 2013 08:08:25 -0700'
/rss/channel/item/dc:date.Taken='2006-10-10T16:37:23-08:00'
/rss/channel/item/author='nobody@flickr.com (IESE Business School)'
/rss/channel/item/author[@flickr:profile='http://www.flickr.com/people/94937385@N08/']
/rss/channel/item/guid='tag:flickr.com,2004:/photo/8639538757'
/rss/channel/item/guid[@isPermaLink='false']
/rss/channel/item/media:content=''
/rss/channel/item/media:content[@url='http://farm9.staticflickr.com/8126/8639538757_65ff26e410_b.jpg']
/rss/channel/item/media:content[@type='image/jpeg']
/rss/channel/item/media:content[@height='681']
/rss/channel/item/media:content[@width='1024']
/rss/channel/item/media:title='IESE North Campus - Q Building'
/rss/channel/item/media:thumbnail=''
/rss/channel/item/media:thumbnail[@url='http://farm9.staticflickr.com/8126/8639538757_65ff26e410_s.jpg']
/rss/channel/item/media:thumbnail[@height='75']
/rss/channel/item/media:thumbnail[@width='75']
XSL;

        $proc = new PreProcessor();
        $fields = $proc->getRecordSchema('/rss/channel/item', explode("\n", $xsl_output));

        $expected = array('pubDate', 'dc:date.Taken', 'author', 'author@flickr:profile', 'guid',
            'guid@isPermaLink', 'media:content', 'media:content@url', 'media:content@type', 'media:content@height', 'media:content@width',
            'media:title', 'media:thumbnail', 'media:thumbnail@url', 'media:thumbnail@height', 'media:thumbnail@width');

        $this->assertEquals( $expected, $fields );

    }

    function testBar()
    {
        $proc = new PreProcessor();
        $new_xpath = $proc->removeAttributeNamespace("author@flickr:profile");
        $this->assertEquals("author@profile", $new_xpath);
    }
}