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

        $record_field_map = array();
        for ($i = 0; $i < count($Worksheet->record_fields); $i++ )
        {
            $record_field_map[$Worksheet->record_fields[$i]] = "a".$i;
        }

        $processor = new PreProcessor();
        $records = $processor->getRecords($Worksheet->record_xpath, $xml_file);
        foreach ($records as $record)
        {
            $out[] = self::bindRecord($record, $Worksheet->mappings, $record_field_map);
        }
        return $out;
    }

    /**
     * @param $record
     * @param $Mappings \model\Mapping[]
     * @return array
     */
    public static function bindRecord($record, $Mappings, $record_field_map=NULL)
    {
        $record_data = array();
        $js_include = "";
        if (isset($record_field_map))
        {
            foreach ($record_field_map as $field => $safeFieldName)
            {
                $field_val = (array_key_exists($field, $record)) ? $record[$field] : "";
                $record_data[$field] = array('safeFieldName' => $safeFieldName, 'value' => str_replace(array("\r\n", "\r", "\n"), "", $field_val));
                $js_include .= "var ".$safeFieldName." = '".$record_data[$field]['value']."'; ";
            }

            $field_keys = array_keys($record_field_map);
            usort($field_keys, function($a, $b){
                $aLen = strlen($a);
                $bLen = strlen($b);

                if ( $aLen > $bLen ) {
                    return -1;
                } else if ( $aLen < $bLen )  {
                    return 1;
                } else {
                    return 0;
                }
            });
        }


        if (class_exists('V8Js'))
        {
            $v8 = new V8Js();
        }

        $out = array();
        foreach ($Mappings as $Mapping)
        {
            $thisFormula = $Mapping->formula;
            if (isset($field_keys))
            {
                foreach ($field_keys as $field)
                {
                    $thisFormula = str_replace($field, $record_data[$field]['safeFieldName'], $thisFormula);
                }
            }

            if (class_exists('V8Js') && isset($record_field_map))
            {
                $out[$Mapping->label] = $v8->executeString($js_include." ".$thisFormula);
            } else {
                $out[$Mapping->label] = array_key_exists($Mapping->formula, $record) ? $record[$Mapping->formula] : "";
            }
        }
        return $out;
    }

}