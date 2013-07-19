<?php

require_once 'TestBase.php';

class SerializerTest extends TestBase
{
    function testFoo()
    {
        $arr = array(
            'foo' => 'bar',
            'monkey' => 'banana',
            'baz' => array(
                'a' => 'a',
                'b' => 'b',
                'c' => array(
                    'd' => 'd',
                    'e' => array(),
                ),
            ),
        );

        $result = Serializer::ArrayToXml($arr);
        $expected = <<<RESULT
<?xml version="1.0" encoding="UTF-8"?>\n<data>\n\t<foo>bar</foo>\n\t<monkey>banana</monkey>\n\t<baz>\n\t\t<a>a</a>\n\t\t<b>b</b>\n\t\t<c>\n\t\t\t<d>d</d>\n\t\t\t<e/>\n\t\t</c>\n\t</baz>\n</data>\n
RESULT;
        $this->assertEquals($expected, $result);
    }
}