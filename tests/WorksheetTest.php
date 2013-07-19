<?php

require_once 'TestBase.php';

class WorksheetTest extends TestBase
{
    public function testSerialize()
    {
        $ws = new \model\Worksheet();
        $ws->module = "foo";
        $ws->record_xpath = "bar";

        $map = new \model\Mapping();
        $map->type = "shit";
        $map->formula = "eat it";
        $map->label = "libelous";
        $map->ours = false;
        $ws->mappings[] = $map;

        $cereal = $ws->serialize();
        $this->assertContains("libelous", $cereal);
        $this->assertContains("bar", $cereal);
    }

    public function testDeserialize()
    {
        $cereal = '{"mappings":[{"label":"libelous","formula":"eat it","type":"shit","ours":false}],"record_xpath":"bar","module":"foo"}';

        $ws = new \model\Worksheet($cereal);
        $this->assertEquals($ws->module, "foo");
        $mapping = $ws->mappings[0];
        $this->assertEquals($mapping->formula, "eat it");
    }
}