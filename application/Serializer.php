<?php


class Serializer
{
    private static $record_type_name;

    public static function ArrayToXml(array $in, $record_type_name)
    {
        self::$record_type_name = $record_type_name;

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
            if (is_numeric($key))
            {
                $name = self::$record_type_name;
            }
            else
            {
                $name = $key;
            }
            $Writer->startElement($name);
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