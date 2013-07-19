<?php

class PreProcessor
{
    /**
     * @var string
     */
    private $contents;

    /**
     * @var SimpleXmlElement
     */
    private $XmlDocument;

    /**
     * @var array
     */
    private $XpathMappings;

    public function getRecords($xml_file, $record_xpath)
    {
        $this->contents = file_get_contents($xml_file);
        $this->XmlDocument = simplexml_load_string($this->contents);

        $Records = $this->XmlDocument->xpath($record_xpath);
        $schema = $this->getRecordSchema(null, $record_xpath);
        $out = array();
        foreach ($Records as $Record)
        {
            $cur = array();
            foreach ($schema as $mapping)
            {
                $simpleXMLElements = $Record->xpath($mapping);
                $cur[$mapping] = trim(array_pop($simpleXMLElements));
            }
            $out[] = $cur;
        }
        return $out;
    }

    /**
     * @return array
     */
    public function getXpathMappings()
    {
        return $this->XpathMappings;
    }

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

    public function getRecordXPath($filename = null)
    {
        if ($filename)
        {
            $this->setContentsFromFilename($filename);
        }

        $all_xpaths = $this->getAllXpathMappings($this->contents, $this->getXslWorksheet());

        $record_path = NULL;
        foreach ($all_xpaths as $p1)
        {
            if (preg_match("/\[[0-9]+\]/", $p1))
            {
                $path_parts = explode("[", $p1);
                $record_path = $path_parts[0];
            }
        }
        return $record_path;
    }

    public function getAllXpathMappings($xml = null, $xsl = null)
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
        $xpath_str = $xslt->transformToXml($this->getXmlDoc($xml));
        $this->XpathMappings = explode("\n", $xpath_str);

        return $this->XpathMappings;
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

    public function getRecordSchema($XPATH = null, $record_xpath = null)
    {
        if ($XPATH == null)
        {
            $XPATH = $this->getAllXpathMappings();
        }

        $schema = array();
        foreach ($XPATH as $path)
        {
            if (strpos($path, $record_xpath) === false)
            {
                continue;
            }

            $pattern = '#' . $record_xpath . '(\[[0-9]+\])?\/#';
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