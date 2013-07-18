<?php

class Transformer
{
    function getRecordNode($filename)
    {
        $xml_body = file_get_contents($filename);
        $xsl_worksheet = file_get_contents(ALL_XSLT);
        $all_xpaths = $this->transform($xml_body, $xsl_worksheet);

        $record_path = NULL;
        foreach (explode("\n", $all_xpaths) as $p1) {
            if (($pos = strpos($p1, "[")) === false) {

            } else {
                $path_parts = explode("[", $p1);
                $record_path = $path_parts[0];
            }
        }
        return $record_path;
    }

    function transform($xml, $xsl)
    {
        $xslt = new XSLTProcessor();
        $xslt->importStylesheet(new SimpleXMLElement($xsl));
        return $xslt->transformToXml(new SimpleXMLElement($xml));
    }

}