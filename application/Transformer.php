<?php

class Transformer
{
    private $contents;
    /**
     * @var SimpleXmlElement
     */
    private $XmlDocument;

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
        foreach (explode("\n", $all_xpaths) as $p1)
        {
            if (preg_match("/\[[0-9]+\]/", $p1))
            {
                $path_parts = explode("[", $p1);
                $record_path = $path_parts[0];
            }
        }
        return $record_path;
    }

    public function transform($xml = null, $xsl = null)
    {
        if (!$xml)
        {
            $xml = $this->contents;
        }
        else
        {
            $this->contents = $xml;
        }
        if (!$xsl)
        {
            $xsl = $this->getXslWorksheet();
        }

        $xslt = new XSLTProcessor();
        $xslt->importStylesheet(new SimpleXMLElement($xsl));
        return $xslt->transformToXml($this->getXmlDoc($xml));
    }

    public function getAllMappingsFromNode($record_path)
    {
        $Records = $this->XmlDocument->xpath($this->getRecordNode());
    }

    public function killCache()
    {
        $this->XmlDocument = null;
    }

    /**
     * @param $xml
     * @return SimpleXMLElement
     */
    public function getXmlDoc($xml)
    {
        if (!$this->XmlDocument)
        {
            $this->XmlDocument = new SimpleXMLElement($xml);
        }
        return $this->XmlDocument;
    }

    public function getRecordSchema($XPATH = null, $prefix = null)
    {
        $schema = array();
        foreach ($XPATH as $path)
        {
            if (strpos($path, $prefix) === false)
            {
                continue;
            }

            $pattern = '#' . $prefix . '(\[[0-9]+\])?\/#';
            $path = preg_replace($pattern, '', $path);
            $pieces = preg_split('/[=\[]/', $path);
            $tag = $pieces[0];

            if (!isset($schema[$tag]))
            {
                $schema[$tag] = $tag;
            }
        }
        return $schema;
    }
}