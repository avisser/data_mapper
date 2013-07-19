<?php
define('APP_PATH', realpath(dirname(__FILE__)));

function my_autoloader($class)
{
    $filename = APP_PATH . '/' .  str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    if (file_exists($filename))
    {
        include($filename);
    }

    return false;
}

spl_autoload_register('my_autoloader');

define('ALL_XSLT', APP_PATH . '/all_xpaths.xsl');
