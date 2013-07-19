<?php

namespace model;

class Worksheet
{
    /**
     * @var Mapping[]
     */
    public $mappings = array();
    public $record_xpath;
    public $record_fields;
    public $module;

    public function __construct($json = null)
    {
        if ($json)
        {
            $this->deserialize($json);
        }
    }

    public function serialize()
    {
        $obj = array(
            'mappings' => $this->mappings,
            'record_xpath' => $this->record_xpath,
            'module' => $this->module);
        return json_encode($obj);
    }

    public function deserialize($json)
    {
        $obj = json_decode($json, true);
        $this->mappings = Mapping::parse($obj['mappings']);
        $this->record_xpath = $obj['record_xpath'];
        $this->module = $obj['module'];
    }
}