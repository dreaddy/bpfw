<?php
/*
 *
 * Copyright (c) 2017-2023. Torsten Lüders
 *
 * Part of the BPFW project. For documentation and support visit https://bpfw.org .
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
 * OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 *
 */

/** @noinspection PhpUnused */

/**
 * defaultListView short summary.
 *
 * defaultListView description.
 *
 * @version 1.0
 * @author torst
 */

require_once(BPFW_MVC_PATH . "views/defaultlistView.inc.php");

class importView extends DefaultListView
{

    /**
     * Summary of renderView
     * @throws Exception
     */
    function renderView(): void
    {

        $this->hasAnyButtons = true;

        if (empty($_GET['action'])) {
            echo "<div style='padding-left:30px'>";
            echo __("Import a JSON File (.zip or .json) to start import.");
            echo "<br>";
            echo "<br>"; ?>

            <form action="?p=import&action=import1" method="POST" enctype="multipart/form-data">
                <input type="file" name="jsonfile" accept=".zip,application/json"/>
                <input type="submit"/>
            </form>

            <?php
            echo "</div>";
        } else if (getorpost("action") == "import1") { // !empty($_FILES["jsonfile"])){

            // require_once(BPFW_MVC_PATH."models/tempfileModel.inc.php");


            $restore_backup_id = (int)getorpost("restore_backup_id");

            $tempfileid = -1;
            if (!empty($restore_backup_id)) {

                $db = bpfw_getDb();
                $backupEntry = $db->makeSelectSingleOrNull("backup", "backupId", $restore_backup_id);

                if ($backupEntry == null) {
                    echo "backup not found";
                    return;
                }


                $outputFilename = '';
                $filename = $backupEntry["pathJson"];

                //$tempfileModel = bpfw_createModelByName("tempfile"); //new TempfileModel();
                //$tempfileModel->cleanTempfiles();

                //$tempfileid = $tempfileModel->DbInsert( array("path"=>$filename, "created"=>time(), "type"=>"importfile", "createdByUser"=>bpfw_getUserId(), "outputFilename"=>$outputFilename ));


            } else {

                $jsonfile = $_FILES["jsonfile"];

                if (empty($jsonfile["tmp_name"])) {
                    echo "invalid import file, maybe too big? Max Filesize is " . ini_get("upload_max_filesize");
                }


                $filename = @tempnam(TEMP_PATH, 'tmp_');

                // echo $filename; die();

                if ($filename !== FALSE) {

                    if (!file_exists(TEMP_PATH)) {
                        mkdir(TEMP_PATH, 0600, true);
                    }

                    $output = file_get_contents($jsonfile["tmp_name"]);

                    /*
                     * // parse zip files later
                     *
                     * if($jsonfile["type"] != "application/json"){

                        $zip = new ZipArchive();

                        $first = true;

                        if ($zip->open($jsonfile["tmp_name"]))
                        {

                            for ($i = 0; $i < $zip->numFiles; $i++) {

                                if($first){

                                    $filename_zip = $zip->getNameIndex($i);

                                    if(pathinfo($filename_zip)['extension'] == "json"){

                                        $zip->close();
                                        $output = file_get_contents("zip://".$jsonfile["tmp_name"]."#".$filename_zip);




                                        $first=false;
                                        break;

                                    }

                                }
                            }

                        }



                    }*/


                    file_put_contents($filename, $output);

                    //echo $filename;
                    $outputFilename = '';

                    $tempfileModel = bpfw_createModelByName("tempfile"); //new TempfileModel();
                    $tempfileModel->cleanTempfiles();

                    bpfw_getDb()->refreshConnection(); // timeout may occure because it took too long to generate the json
                    $tempfileid = $tempfileModel->DbInsert(array("path" => $filename, "created" => time(), "type" => "importfile", "createdByUser" => bpfw_getUserId(), "outputFilename" => $outputFilename));

                } else {

                    throw new Exception("temp filename failed");

                }


            }

            // echo file_get_contents("./../backups/\\726 (2021-05-01)\\db\\backup_db_2021-05-01_9fa27034f507d96ce7f572b8be4256.json");

            //$filename=str_replace("./../","",$filename);
            //$filename = BASE_PATH.$filename;

            $tables = array();

            if ($jsonfile["type"] == "application/json") {
                $json = file_get_contents($filename);

                $data = json_decode($json, true);

                if (!isset($data["data"])) {
                    echo "invalid import file";
                    echo json_last_error();
                    echo "<br>";
                    echo json_last_error_msg();
                    echo "<br>";
                    die();
                }


                foreach ($data["data"] as $tablename => $values) {

                    $tables[$tablename] = array("entries" => count($values));

                }


            } else {
                // zipfile,


                $zip = new ZipArchive();

                $first = true;

                if ($zip->open($jsonfile["tmp_name"])) {

                    for ($i = 0; $i < $zip->numFiles; $i++) {


                        $filename_zip = $zip->getNameIndex($i);

                        if (pathinfo($filename_zip)['extension'] == "json") {

                            $output = file_get_contents("zip://" . $jsonfile["tmp_name"] . "#" . $filename_zip);

                            $data = json_decode($output, true);

                            if (!isset($data["data"])) {
                                echo "<p><b>Warning: invalid data in file $filename_zip inside of zip</b><br>";
                                echo json_last_error();
                                echo "<br>";
                                echo json_last_error_msg();
                                echo "</p><br>";

                            }

                            $tablename = pathinfo($filename_zip)["filename"];

                            foreach ($data["data"] as $tablename => $values) {

                                $tables[$tablename] = array("entries" => count($values));

                            }

                        }

                    }


                    $zip->close();

                }
            }


            //  var_dump($data);

            if (empty($tables)) {
                echo "invalid import file 2";
                echo json_last_error();
                echo "<br>";
                echo json_last_error_msg();
                echo "<br>";
            } else {


                ?>


                <div style="width:90%;margin:auto;display:block;">

                    <h5><?php echo __("found"); ?>:</h5>

                    <div style="border:1px solid black; padding:15px; ">

                        <form action="?p=import&action=import2&restore_backup_id=<?php echo $restore_backup_id; ?>"
                              method="POST">
                            <table style="width:100%">
                                <?php

                                echo "<tr style='border-bottom:1px solid black;'>";

                                echo "<th>";
                                echo __("Amount");
                                echo "</th>";
                                echo "<th>";
                                echo __("Name");
                                echo "</th>";
                                echo "<th>";
                                echo __("Replace contents(delete all old data)");
                                echo "</th>";

                                echo "<th>";
                                echo __("Merge contents(replace old data if it exists in backup)");
                                echo "</th>";

                                echo "<th>";
                                echo __("Complete contents(only add new data row doesnt exist in old data)");
                                echo "</th>";

                                echo "<th>";
                                echo __("Ignore backup for this table");
                                echo "</th>";

                                echo "</tr>";

                                $models = $this->model->getAllModelsWithTable();

                                $processValues = array();

                                foreach ($tables as $tablename => $values) {

                                    $model = bpfw_createModelByName($tablename);

                                    if (!!$model->ignoreOnImportExport) {
                                        continue;
                                    }

                                    //foreach($tabledata as $tablename=>$values){

                                    $processValues = $values;

                                    /*$processValues[$tablename] = array();

                                    foreach($values as $rowno=>$rowentries){

                                        $processValues[$tablename][$rowno] = array();

                                        foreach($rowentries as $key=>$value){
                                            if(!is_array($value) && !is_numeric($value)){
                                                $processValues[$tablename][$rowno][$key] = bpfw_htmlentities($value);
                                            }else{
                                                $processValues[$tablename][$rowno][$key] = $value;
                                            }
                                        }
                                    }

                                    var_dump($processValues);*/

                                    echo "<tr>";

                                    if (!isset($models[$tablename])) {
                                        echo "<tr><td colspan='5'><b style='color:red'>Achtung: $tablename gefunden, existiert aber nicht. Wird ignoriert.</b></td></tr>";
                                        continue;
                                    }

                                    echo "<td>";
                                    echo $processValues["entries"];
                                    echo "</td>";

                                    echo "<td>";
                                    echo $tablename;
                                    echo "</td>";

                                    echo "<td>";
                                    echo "<input value='delete_override;" . $tempfileid . "' type='radio' name='" . $tablename . "' checked>";
                                    echo "</td>";

                                    echo "<td>";
                                    echo "<input value='merge_override;" . $tempfileid . "' type='radio' name='" . $tablename . "'>";
                                    echo "</td>";

                                    echo "<td>";
                                    echo "<input value='merge_add;" . $tempfileid . "' type='radio' name='" . $tablename . "'>";
                                    echo "</td>";


                                    echo "<td>";
                                    echo "<input value='ignore;' type='radio' name='" . $tablename . "'>";
                                    echo "</td>";

                                    echo "</tr>";

                                    //    }

                                }

                                ?>
                            </table>

                            <br/>

                            <input type="submit"/>

                        </form>
                    </div>

                </div>

                <?php


            }

        } else {

            require_once(BPFW_MVC_PATH . "models/tempfileModel.inc.php");

            if (isset($_GET["action"]) && $_GET["action"] == "import2") {

                $models = $this->model->getAllModelsWithTable();

                $dataFull = null;

                $restore_backup_id = (int)getorpost("restore_backup_id");

                if (empty($_POST)) {
                    echo "<div style='padding-left:12px;'>".__("Nothing found to restore")."</div>";
                    return;
                }

                foreach ($_POST as $tablename => $value) {

                    bpfw_getDb()->convertTableCharsetToUtf8($tablename);

                    $currentmodel = $models[$tablename];

                    $linkfields = $currentmodel->getAllLinkTableFields();

                    foreach ($linkfields as $linkfieldkey => $linkfieldvalue) {

                        $linktablename = $currentmodel->getLinkTableName($linkfieldkey);

                        bpfw_getDb()->convertTableCharsetToUtf8($linktablename);

                    }


                    if (!isset($models[$tablename])) {
                        echo "<tr><td colspan='5'><b style='color:red'>Achtung: $tablename gefunden, existiert aber nicht. Wird ignoriert.</b></td></tr>";
                        continue;
                    }

                    //   echo $models[$tablename];

                    echo "<pre>";


                    if (bpfw_strStartsWith($value, "delete_override;")) {


                        $tempfileid = mb_substr($value, 1 + strpos($value, ";"));


                        //$json=mb_substr($value, 1 + strpos($value,";"));
                        //$data = json_decode($json, true);


                        $files = array();

                        if (empty($dataFull["data"][$tablename])) {

                            if (empty($restore_backup_id)) {

                                $tempfilemodel = bpfw_createModelByName("tempfile"); //new TempfileModel();
                                $tempfiledata = $tempfilemodel->DbSelectSingleOrNullByKey($tempfileid);
                                $jsonfile = $tempfiledata["path"];

                                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                                $mime = finfo_file($finfo, $jsonfile);
                                finfo_close($finfo);


                                if ($mime == "application/zip") {

                                    $filename_zip = $tablename . ".json";
                                    // $output = file_get_contents("zip://".$jsonfile."#".$filename_zip);

                                    $files[] = "zip://" . $jsonfile . "#" . $filename_zip;

                                } else if ($mime == "application/json") {
                                    $files[] = $jsonfile;
                                }


                                // var_dump($files);


                            } else {

                                $db = bpfw_getDb();
                                $backupEntry = $db->makeSelectSingleOrNull("backup", "backupId", $restore_backup_id);

                                if ($backupEntry == null) {
                                    echo "backup not found";
                                    return;
                                }

                                $outputFilename = '';
                                $filename = $backupEntry["pathJson"];

                                $files[] = $filename;

//                                  $json = file_get_contents($filename);
                                //$dataFull = json_decode($json, true);

                            }
                        }


                        foreach ($files as $file) {

                            $json = file_get_contents($file);
                            $dataFull = json_decode($json, true);

                            foreach ($dataFull["data"] as $tablename2 => $data) {

                                // foreach($tabledata as $tablename2=>$data){

                                if ($tablename2 != $tablename) continue;
                                else {


                                    if ($data !== NULL) {
                                        $models[$tablename]->importData($data, "delete_override");
                                        $entries = count($data);
                                        echo "<tr><td colspan='5'><b style='color:green'>$tablename: $entries Datensätze verarbeitet (löschen und überschreiben).</b></td></tr>";
                                    } else {
                                        echo "<tr><td colspan='5'><b style='color:red'>Achtung: $tablename import fehlgeschlagen, json ungültig.<br>";
                                        echo match (json_last_error()) {
                                            JSON_ERROR_NONE => ' - Keine Fehler',
                                            JSON_ERROR_DEPTH => ' - Maximale Stacktiefe überschritten',
                                            JSON_ERROR_STATE_MISMATCH => ' - Unterlauf oder Nichtübereinstimmung der Modi',
                                            JSON_ERROR_CTRL_CHAR => ' - Unerwartetes Steuerzeichen gefunden',
                                            JSON_ERROR_SYNTAX => ' - Syntaxfehler, ungültiges JSON',
                                            JSON_ERROR_UTF8 => ' - Missgestaltete UTF-8 Zeichen, möglicherweise fehlerhaft kodiert',
                                            default => ' - Unbekannter Fehler',
                                        };
                                        echo "<br>";
                                        echo "JSON war: <pre>" . $json . "</pre>";
                                        echo "</b></td></tr>";
                                        continue;
                                    }

                                }


                                if (bpfw_strStartsWith($value, "merge_override;")) {

                                    if (empty($dataFull)) {
                                        $json = file_get_contents($jsonfile);
                                        $dataFull = json_decode($json, true);
                                    }

                                    $data = $dataFull[$data];

                                    if ($data !== NULL) {
                                        $models[$tablename]->importData($data, "merge_override");
                                        $entries = count($data);
                                        echo "<tr><td colspan='5'><b style='color:green'>$tablename: $entries Datensätze verarbeitet (überschreiben).</b></td></tr>";
                                    } else {
                                        echo "<tr><td colspan='5'><b style='color:red'>Achtung: $tablename import fehlgeschlagen.</b></td></tr>";
                                        continue;
                                    }

                                }

                                if (bpfw_strStartsWith($value, "merge_add;")) {

                                    if (empty($dataFull)) {
                                        if (!empty($jsonfile)) {
                                            $json = file_get_contents($jsonfile);
                                            $dataFull = json_decode($json, true);
                                        } else {
                                            throw new Exception("jsonfile is empty");
                                        }
                                    }

                                    $data = $dataFull[$data];

                                    if ($data !== NULL) {
                                        $models[$tablename]->importData($data, "merge_add");
                                        $entries = count($data);
                                        echo "<tr><td colspan='5'><b style='color:green'>$tablename: $entries Datensätze verarbeitet (hinzufügen).</b></td></tr>";
                                    } else {
                                        echo "<tr><td colspan='5'><b style='color:red'>Achtung: $tablename import fehlgeschlagen.</b></td></tr>";
                                    }

                                }
                            }

                        }

                        //   }

                    }

                    //$models[$tablename]->importData($tablename);

                    echo "</pre>";

                }
            }
        }

        // array(1) { ["jsonfile"]=> array(5) { ["name"]=> string(19) "all_20.02.2019.json" ["type"]=> string(16) "application/json" ["tmp_name"]=> string(49) "D:\work\abdu\stundenerfassung\srv\tmp\phpB7F1.tmp" ["error"]=> int(0) ["size"]=> int(145233) } }

        /* var_dump($_FILES);

         var_dump($_POST); */


        //bpfw_add_action(DefaultlistView::ACTION_DEFAULTLISTVIEW_BEVORE_RENDER_TABLE, $this->model->GetTableName(), array($this, "extraButtons"));

        //bpfw_add_action(DefaultlistView::ACTION_DEFAULTLISTVIEW_DISPLAY_BUTTONS, $this->model->GetTableName(), array($this, "extraButtons"), 10, 2 );

        //parent::renderView();

    }


}