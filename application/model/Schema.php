<?php

namespace model;

class Schema
{
    public static function load($model)
    {
        return json_decode(file_get_contents(APP_PATH."/../schema/$model.json"), true);
    }

    public static function all()
    {
        $files = glob(APP_PATH . "/../schema/*.json");
        $massaged = array();
        foreach ($files as $file)
        {
            $basename = basename($file);
            $pieces = explode('.', $basename);
            $massaged[] = $pieces[0];
        }

        return $massaged;
    }
}