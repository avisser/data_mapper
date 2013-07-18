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

$xsl_worksheet = file_get_contents('all_xpaths.xsl');
$xml_input = file_get_contents('../sample_data/photos_rss.xml');

print_r( transform($xml_input, $xsl_worksheet) );
