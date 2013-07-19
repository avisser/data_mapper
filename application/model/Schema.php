<?php

namespace model;

class Schema
{
    public static function load($model)
    {
        return json_decode(file_get_contents(APP_PATH."/../schema/$model.json"));
    }
}