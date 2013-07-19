<?php

require_once 'app.php';

class Transformer
{
    /**
     * @param $worksheet
     * @param $xml_file
     * @return array
     */
    public static function react(model\Worksheet $worksheet, $xml_file)
    {
        $records = PreProcessor::getRecords( $xml_file, $worksheet->record_xpath );
        foreach ($records as $record)
        {

        }
        //run worksheet.record_xpath against $xml_file
        //foreach result
        //    bindRecord(result, mapping)
        //endeach
    }

    /**
     * @param $record
     * @param $mappings array
     * @return array
     */
    public static function bindRecord($record, $mappings)
    {
        //  foreach $worksheet.mapping
        //    run mapping.record_xpath against $xml_file
        //  endeach
    }

}