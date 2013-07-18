<?php
/**
 * @param  $xml
 * @param  $xsl
 * @return string xml
 */

function transform($xml, $xsl) {
   $xslt = new XSLTProcessor();
   $xslt->importStylesheet(new  SimpleXMLElement($xsl));
   return $xslt->transformToXml(new SimpleXMLElement($xml));
}

$xsl_worksheet = file_get_contents('all_xpaths.xsl');
$sample_xml_files = array('../sample_data/photos_rss.xml', '../sample_data/input2.xml');


foreach ($sample_xml_files as $xml_file)
{
	$xml_input = file_get_contents($xml_file);
	$all_xpaths =  transform($xml_input, $xsl_worksheet);

	$record_path = null;
	foreach (explode("\n", $all_xpaths) as $p1)
	{
		if (($pos = strpos($p1, "[")) === false ) {

		} else {
			$path_parts = explode("[",$p1);
			$record_path = $path_parts[0];
		}
	}

	if ( isset($record_path) ) {
		print "$xml_file: Record path: $record_path\n";
	} else {
		print "$xml_file: Record path NOT FOUND!\n";
	}
}
?>
