<?php
require_once __DIR__ . '/../application/app.php';

$f3 = require(__DIR__ . '/../fatfree/lib/base.php');
$f3->set('AUTOLOAD', __DIR__ . '/../fatfree/lib/web/');
$f3->set('UI', __DIR__.'/templates/');
$f3->set('DEBUG', 3);

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

        $processor = new PreProcessor();
        $processor->setContentsFromFilename($tmp_file);
        $xpath = $processor->getRecordXPath();
        $record_fields = $processor->getRecordSchema($xpath);
        $module = $_POST['module'];
        $schema = model\Schema::load($module);//$f3->get("PARAMS['module']"));
        $f3->set("xpath", $xpath);
        $f3->set("record_fields", $record_fields);
        $f3->set("schema", $schema);
        $f3->set("module", $module);

        $template = new Template();
        echo $template->render('worksheet.htm');
    });
$f3->run();