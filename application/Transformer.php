<?php

require_once 'app.php';

class Transformer
{
    /**
     * @param $xml_file
     * @param \model\Worksheet $Worksheet
     * @return array
     */
    public static function react($xml_file, model\Worksheet $Worksheet)
    {
        $out = array();

        $processor = new PreProcessor();
        $records = $processor->getRecords($xml_file, $Worksheet->record_xpath);
        foreach ($records as $record)
        {
            $out[] = self::bindRecord($record, $Worksheet->mappings);
        }
        return $out;
    }

    /**
     * @param $record
     * @param $Mappings \model\Mapping[]
     * @return array
     */
    public static function bindRecord($record, $Mappings)
    {
        $out = array();
        foreach ($Mappings as $Mapping)
        {
            $out[$Mapping->label] = $record[$Mapping->formula];
        }
        return $out;
    }

}