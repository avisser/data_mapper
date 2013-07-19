<?php

namespace model;

class Mapping
{
    public $label;
    public $formula;
    public $type;
    public $ours = true;

    public function __construct($json = null)
    {
        if ($json)
        {
            $this->deserialize($json);
        }
    }

    public function deserialize($assoc)
    {
        $this->label = $assoc['label'];
        $this->formula = $assoc['formula'];
        $this->type = $assoc['type'];
        $this->ours = (bool)$assoc['ours'];
    }

    public static function parse($mappings_assoc)
    {
        $mappings = array();
        foreach ($mappings_assoc as $mapping)
        {
            $map = new Mapping($mapping);
            $mappings[] = $map;
        }
        return $mappings;
    }
}