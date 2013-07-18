<?php

class Transformer
{
    private $contents;

    public function setContents($contents)
    {
        $this->contents = $contents;
    }

    public function setContentsFromFilename($filename)
    {
        $this->contents = file_get_contents($filename);
    }

    public function getXslWorksheet()
    {
        return file_get_contents(ALL_XSLT);
    }

    public function getRecordNode($filename = null)
    {
        if ($filename)
        {
            $this->setContentsFromFilename($filename);
        }

        $all_xpaths = $this->transform($this->contents, $this->getXslWorksheet());

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