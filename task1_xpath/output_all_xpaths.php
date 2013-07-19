<?php
/**
* @param $xml
* @param $xsl
* @return string xml
*/
function transform($xml, $xsl) {
   $xslt = new XSLTProcessor();
   $xslt->importStylesheet(new SimpleXMLElement($xsl));
   return $xslt->transformToXml(new SimpleXMLElement($xml));
}

$xsl_worksheet = file_get_contents(__DIR__.'/../application/all_xpaths.xsl');
$xml_input = file_get_contents(__DIR__.'/../sample_data/buildings3.xml');

print_r( transform($xml_input, $xsl_worksheet) );
