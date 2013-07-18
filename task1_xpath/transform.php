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
$sample_xml_files = array('../sample_data/photos_rss.xml', '../sample_data/input2.xml', '../sample_data/buildings1.xml', '../sample_data/buildings2.xml');


foreach ($sample_xml_files as $xml_file)
{
	$xml_input = file_get_contents($xml_file);
	$t1 = microtime(true);
	$all_xpaths =  transform($xml_input, $xsl_worksheet);
	$t2 = microtime(true);
	print "Transform completed in: ".($t2-$t1)."\n";

	$record_path = null;
	foreach (explode("\n", $all_xpaths) as $p1)
	{
		if (preg_match("/\[[0-9]+\]/", $p1)) {
			$path_parts = explode("[",$p1);
			$record_path = $path_parts[0];
			break;
		}
	}

	if ( isset($record_path) ) {
		print "$xml_file: Record path: $record_path\n";
	} else {
		print "$xml_file: Record path NOT FOUND!\n";
	}
}
?>
