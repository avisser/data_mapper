<?php
require_once __DIR__ . '/../application/app.php';

$f3 = require(__DIR__ . '/../fatfree/lib/base.php');
$f3->set('AUTOLOAD', __DIR__ . '/../fatfree/lib/web/');
$f3->set('UI', __DIR__.'/templates/');
$f3->set('DEBUG', 3);
$f3->set('dump', function($a) {
    echo print_r($a, true);
});
$f3->set('ifset', function($a, $b) {
    if (isset($a[$b]))
        echo $a[$b];
});

/**
 * @return string
 */
function getWorksheetDir()
{
    $worksheet_dir = APP_PATH . "/../worksheets";
    if (!stat($worksheet_dir)) {
        mkdir($worksheet_dir);
        return $worksheet_dir;
    }
    return $worksheet_dir;
}


//$f3->route('GET /',
//function () {
//    echo 'Hello, world!';
//});

$f3->route('GET /',
    function ($f3) {
        $template = new Template();
        $schemas = array('Maps', 'Photos');
        $schemas = model\Schema::all();
        $f3->set('schemas', $schemas);
        echo $template->render('index.htm');
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
        $records = $processor->getRecords($xpath);
        $module = $_POST['module'];
        $schema = model\Schema::load($module);
        $f3->set("xpath", $xpath);
        $f3->set("record_name", $schema['recordNode']);
        $f3->set("record_fields", $record_fields);
        $f3->set("first_record", $records[0]);
        $f3->set("record_fields_arr", json_encode($record_fields));
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
        $f3->set('module', $module);

        $record_fields = json_decode($_POST['record_fields_arr'], true);

        $ws = new model\Worksheet();
        $ws->module = $module;
        $ws->record_xpath = $_POST['xpath'];
        $ws->record_fields = array_values($record_fields);

        foreach ($schema['fields'] as $field)
        {
            $formula = $_POST[$field['name']];
            if ($formula != '-')
            {
                $mapping = new model\Mapping();
                $mapping->formula = ($formula == 'custom') ? $_POST[$field['name']."_custom"] : $formula;
                $mapping->label = $field['name'];
                $mapping->ours = true;
                $mapping->type = $field['type'];

                $ws->mappings[] = $mapping;
            }
        }

        $xml_file = $_POST['tmp_file'];
        $transformed_data = Transformer::react($xml_file, $ws);

        $f3->set('ESCAPE',FALSE);
        $f3->set('tmp_file', $xml_file);
        $f3->set('ws', $ws);
        $f3->set('transformed_data', $transformed_data);
        $transformed_xml = $f3->set('transformed_data_xml', Serializer::ArrayToXml($transformed_data, $schema['recordNode']));
        $f3->set('input_data_xml', file_get_contents($xml_file));

        $new_tmp_file = tempnam(APP_PATH . '/../tmp/', $module);
        file_put_contents("$new_tmp_file.xml", $transformed_xml);
        $f3->set('dl_xml_path', basename($new_tmp_file));

        $worksheet_dir = getWorksheetDir();
        $worksheet_name = isset($_POST['ws_name']) ? $_POST['ws_name'] : 'no_name';

        $ws_path = "$worksheet_dir/$worksheet_name.json";
        file_put_contents($ws_path, $ws->serialize());
        $f3->set('worksheet', $worksheet_name);

        $template = new Template();
        echo $template->render('mapped.htm');

    });

$f3->route('GET /xml/@filename',
    function($f3, $args) {
        $filename = $args['filename'];
        if (!Web::instance()->send(APP_PATH . "/../tmp/$filename.xml"))
            // Generate an HTTP 404
        $f3->error(404);
    });

$f3->route('GET /worksheet/@filename',
    function($f3, $args)  {
        $filename = $args['filename'];
        if (!Web::instance()->send(getWorksheetDir()."/$filename.json"))
            // Generate an HTTP 404
        $f3->error(404);
    });
$f3->run();