<?php
namespace FieldMap;

class Generator
{
    private $XML;
    private $record_xpath;

    /**
     * @var \SimpleXmlElement[]
     */
    private $Records;

    private $record_fields = array();

    public function __construct(\SimpleXMLElement $SimpleXml)
    {
        $this->XML = $SimpleXml;
    }

    public function setRecordXpath($xpath)
    {
        if ($xpath != $this->record_xpath)
        {
            $this->record_xpath = $xpath;
            $this->record_fields = array();
        }
    }

    public function findRecords()
    {
        $this->Records = $this->XML->xpath($this->record_xpath);
    }

    public function getRecords()
    {
        return $this->Records;
    }

    public function generateMappings()
    {
        if (!$this->Records)
        {
            $this->findRecords();
        }
        foreach ($this->Records as $Record)
        {
            foreach ($Record->children() as $Field)
            {
                /**
                 * @var $Field \SimpleXmlElement
                 */
                if (!isset($this->record_fields[$Field->getName()]))
                {
                    $this->record_fields[$Field->getName()] = $Field->getName();
                }
            }
        }
    }

    public function getRecordFields()
    {
        return array_values($this->record_fields);
    }
}