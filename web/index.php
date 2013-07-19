<?php
require_once __DIR__ . '/../application/app.php';

$f3 = require(__DIR__ . '/../fatfree/lib/base.php');
$f3->set('AUTOLOAD', __DIR__ . '/../fatfree/lib/web/');
$f3->set('UI', __DIR__.'/templates/');
$f3->set('DEBUG', 3);
$f3->set('dump', function($a) {
    echo print_r($a, true);
});


//$f3->route('GET /',
//function () {
//    echo 'Hello, world!';
//});

$f3->route('GET /',
    function () {
        $template = new Template();
        echo $template->render('template.htm');
    });

$f3->route('POST /map',
    function () use ($f3) {
        //write file
        $tmp_file = $_FILES['file']['tmp_name'];
        $new_tmp_file = tempnam(APP_PATH . '/../tmp/', '');
        move_uploaded_file($tmp_file, $new_tmp_file);

        $processor = new PreProcessor();
        $processor->setContentsFromFilename($new_tmp_file);
        $xpath = $processor->getRecordXPath();
        $record_fields = $processor->getRecordSchema($xpath);
        $module = $_POST['module'];
        $schema = model\Schema::load($module);
        $f3->set("xpath", $xpath);
        $f3->set("record_fields", $record_fields);
        $f3->set("schema", $schema);
        $f3->set("module", $module);
        $f3->set("tmp_file", $new_tmp_file);

        $template = new Template();
        echo $template->render('worksheet.htm');
    });

$f3->route('POST /mapped',
    function () use ($f3) {
        //foreach entry in the schema
        $module = $_POST['module'];
        $schema = model\Schema::load($module);

        $ws = new model\Worksheet();
        $ws->module = $module;
        $ws->record_xpath = $_POST['xpath'];

        foreach ($schema['fields'] as $field)
        {
            $mapping = new model\Mapping();
            $mapping->formula = $_POST[$field['name']];
            $mapping->label = $field['name'];
            $mapping->ours = true;
            $mapping->type = $field['type'];

            $ws->mappings[] = $mapping;
        }

        $xml_file = $_POST['tmp_file'];
        $transformed_data = Transformer::react($xml_file, $ws);

        $f3->set('ws', $ws);
        $f3->set('transformed_data', $transformed_data);

        $template = new Template();
        echo $template->render('mapped.htm');

    });
$f3->run();