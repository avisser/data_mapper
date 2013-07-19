<?php

require_once 'TestBase.php';

class SerializerTest extends TestBase
{
    function testFoo()
    {
        $arr = array(
            array(
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
            ),
            array(
                'foo' => 'foo',
                'monkey' => 'monkey',
                'baz' => array(
                    'a' => 'a',
                    'b' => 'b',
                    'c' => array(
                        'd' => 'd',
                        'e' => array(),
                    ),
                ),
            ),
        );

        $result = Serializer::ArrayToXml($arr, 'test_case');
        $expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>\n<data>\n\t<test_case>\n\t\t<foo>bar</foo>\n\t\t<monkey>banana</monkey>\n\t\t<baz>\n\t\t\t<a>a</a>\n\t\t\t<b>b</b>\n\t\t\t<c>\n\t\t\t\t<d>d</d>\n\t\t\t\t<e/>\n\t\t\t</c>\n\t\t</baz>\n\t</test_case>\n\t<test_case>\n\t\t<foo>foo</foo>\n\t\t<monkey>monkey</monkey>\n\t\t<baz>\n\t\t\t<a>a</a>\n\t\t\t<b>b</b>\n\t\t\t<c>\n\t\t\t\t<d>d</d>\n\t\t\t\t<e/>\n\t\t\t</c>\n\t\t</baz>\n\t</test_case>\n</data>\n
XML;
        $this->assertEquals($expected, $result);
    }
}