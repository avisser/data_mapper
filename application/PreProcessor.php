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

    public function getRecords($record_xpath, $xml_file = null)
    {
        if ( $xml_file != null )
        {
            $this->setContentsFromFilename($xml_file);
        }
        $this->XmlDocument = simplexml_load_string($this->contents);    //  todo this can be cached

        $Records = $this->XmlDocument->xpath($record_xpath);
        $schema = $this->getRecordSchema($record_xpath, null);
        $out = array();
        foreach ($Records as $Record)
        {
            $cur = array();
            foreach ($schema as $mapping)
            {
                $simpleXMLElements = $Record->xpath($mapping);
                if (strstr($mapping, '@'))
                {
                    if ( count($simpleXMLElements) > 0)
                    {
                        $fuck_simple_xml = $simpleXMLElements[0]->asXml();
                        $attrs = explode('@', $mapping);
                        $attr_name = $attrs[1]; //find the attribute name
                        $pieces = explode(']', $attr_name);
                        $attr_name = $pieces[0]; //remove the trailing ]
                        $value = preg_replace("#.*$attr_name=\"(.*?)\".*#", "$1", $fuck_simple_xml); //parse it out of the xml
                        $cur[$mapping] = $value;
                    }
                }
                else
                {
                    $cur[$mapping] = trim(array_pop($simpleXMLElements));
                }
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
        if (!$this->XpathMappings)
        {
            return $this->getAllXpathMappings();
        }
        return $this->XpathMappings;
    }

    /**
     * @param $contents
     */
    public function setContents($contents)
    {
        if ($this->contents != $contents)
        {
            $this->contents = $contents;
            $this->killCache();
        }
    }

    /**
     * @param $filename
     */
    public function setContentsFromFilename($filename)
    {
        $new = file_get_contents($filename);
        $this->setContents($new);
    }

    /**
     * @return string
     */
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

        $all_xpaths = $this->getAllXpathMappings();

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
        if ($xml)
        {
            $this->setContents($xml);
        }

        if (!$xsl)
        {
            $xsl = $this->getXslWorksheet();
        }

        $xslt = new XSLTProcessor();
        $xslt->importStylesheet(new SimpleXMLElement($xsl));
        $xpath_str = $xslt->transformToXml($this->getXmlDoc());
        $this->XpathMappings = explode("\n", $xpath_str);

        return $this->XpathMappings;
    }

    public function killCache()
    {
        $this->XmlDocument = null;
        $this->XpathMappings = array();
    }

    /**
     * @return SimpleXMLElement
     */
    public function getXmlDoc()
    {
        if (!$this->XmlDocument)
        {
            $this->XmlDocument = new SimpleXMLElement($this->contents);
        }
        return $this->XmlDocument;
    }

    public function getRecordSchema($record_xpath = null, $XPATH = null)
    {
        if ($XPATH == null)
        {
            $XPATH = $this->getXpathMappings();
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
            $pieces = explode('=', $path);
            $tag = $pieces[0];
            if ( strstr($tag, "[@") )
            {
                $tag = "$tag]";
                $tag = $this->removeAttributeNamespace($tag);
            }


            if (!isset($schema[$tag]))
            {
                $schema[] = $tag;
            }
        }
        return array_unique($schema);
    }

    /**
     * @param $record_xpath
     * @return mixed
     */
    public function removeAttributeNamespace($record_xpath)
    {
        $attribute_xpath = preg_replace("#(.*)@(.*):(.*)#", "$1@$3", $record_xpath);
        if (strlen($attribute_xpath) > 0) {
            $record_xpath = $attribute_xpath;
            return $record_xpath;
        }
        return $record_xpath;
    }
}