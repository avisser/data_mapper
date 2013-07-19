<?php


class Serializer
{
    public static function ArrayToXml(array $in)
    {
        $Writer = new XMLWriter;
        $Writer->openMemory();
        $Writer->setIndent(true);
        $Writer->setIndentString("\t");
        $Writer->startDocument('1.0', 'UTF-8');
        $Writer->startElement('data');

        self::ArrayToXmlRecursively($Writer, $in);

        $Writer->endElement();
        $Writer->endDocument();

        return $Writer->outputMemory(true);
    }

    private static function ArrayToXmlRecursively(XMLWriter $Writer, array $data)
    {
        foreach ($data as $key => $value)
        {
            $Writer->startElement($key);
            if (is_array($value))
            {
                self::ArrayToXmlRecursively($Writer, $value);
            }
            else
            {
                $Writer->text($value);
            }
            $Writer->endElement();
        }
    }
}