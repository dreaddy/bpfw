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

use JetBrains\PhpStorm\NoReturn;

/**
 * defaultListView short summary.
 *
 * defaultListView description.
 *
 * @version 1.0
 * @author torst
 */
class ExportControl extends DefaultControl
{

    /**
     * Takes in a filename and an array associative data array and outputs a csv file
     * @param string $fileName
     * @param array $assocDataArray
     */
    public function outputCsv(string $fileName, array $assocDataArray)
    {
        ob_clean();
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=' . $fileName);
        if (isset($assocDataArray['0'])) {
            $fp = fopen('php://output', 'w');
            fputcsv($fp, array_keys($assocDataArray['0']));
            foreach ($assocDataArray as $values) {
                fputcsv($fp, $values);
            }
            fclose($fp);
        }
        ob_flush();

    }

    //var $generatePdfPrint = true;

    #[NoReturn] function handleAjaxCommand(string $command): void
    {


        if ($command == "xls" || $command == "userdata" || $command == "customerdata" || $command == "eventtemplatenumberdata" || $command == "csv2" || $command == "csv" || $command == "csv2" || $command == "zohocsveventexport" || $command == "json") {
            $ouput = $this->getExportData($command, true, $_GET['model']);
            echo $ouput;
        } else {
            parent::handleAjaxCommand($command);
        }
        die();

    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    function getExportData($command, $setHeader = true, $modelname = "all", $wrapJsonInData = true): bool|string|null
    {

        ob_start();

        global $database;
        $model = $this->model;

        if (empty($model)) {
            $this->model = bpfw_createModelByName("export");
            $model = $this->model;
        }

        $allModels = $model->getAllModelsWithTable();

        $currentModels = array();

        if ($modelname != "all" && !empty($allModels[$modelname])) {
            $currentModels[$modelname] = $allModels[$modelname];
        } else if ($modelname == "all") {
            $currentModels = $allModels;
        } else {
            throw new Exception("Model not Existing");
        }


        $csvvalues = array();

        $jsonarray = array();

        if ($wrapJsonInData) {
            $jsonarray = array("data" => NULL);
        }


        if ($command == "csv" || $command == "csv2" || $command == "zohocsveventexport") {

            $suffix = "plaincsv";

            if ($command == "csv2") {
                $suffix = "prettycsv";
            }

            if ($command == "zohocsveventexport") {
                $suffix = "zohocsv";
            }
            if ($setHeader) {
                header('Pragma: public');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Cache-Control: private', false);
                header('Content-disposition: attachment; filename=' . $modelname . '_' . $suffix . '_' . date("d.m.Y") . '.csv');
                header('Content-type: application/csv');
            }
            //   $fp = fopen('php://output', 'w');

        } else if ($command == "xls" || $command == "userdata" || $command == "customerdata" || $command == "eventtemplatenumberdata" || $command == "csv2") {

            if ($setHeader) {

                header('Pragma: public');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Cache-Control: private', false);

                if ($command == "userdata" || $command == "customerdata" || $command == "eventtemplatenumberdata" || $command == "csv2") {
                    header('Content-Type: text/html; charset=utf-8');
                } else { /*if($command=="xls") {*/
                    header('Content-disposition: attachment; filename=' . $modelname . $command . "_" . date("d.m.Y") . '.xls');
                    //header('Content-Type: text/html; charset=utf-8');
                    header('Content-type: application/vnd.ms-excel; charset=utf-8');
                }

            }

        } else if ($command == "json") {

            if ($setHeader) {
                header('Pragma: public');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Cache-Control: private', false);
                header('Content-disposition: attachment; filename=' . $modelname . '_' . date("d.m.Y") . '.json');
                header('Content-type: application/json');
            }

            // echo '{"data":[';

        }


        $values = array();
        foreach ($currentModels as $mkey => $currentModel) {


            $model = bpfw_createModelByName($mkey);

            if ($model->ignoreOnImportExport) {
                continue;
            }

            if ($command == "csv2") {

                $entries_temp = array();

                foreach ($currentModel->getEntries() as $key => $value) {

                    $entries_temp[$key] = array();

                    foreach ($currentModel->getDbModel() as $hkey => $hvalue) {

                        $componentinfo = bpfw_getComponentHandler()->getComponent($hvalue->display);

                        $val = $componentinfo->GetDisplayFormattedPlainValue($value->$hkey, $hkey, $hvalue, $key, $currentModel);

                        //if($hvalue->display == "datepicker"){

                        // var_dump($componentinfo);die();

                        // echo $val;

                        //}

                        $entries_temp[$key][$hkey] = $val;

                        // echo $val;

                    }

                }

                $values[$currentModel->getTableName()] = $entries_temp;

            } else if ($command == "zohocsveventexport") {


                $eventModel = $this->model->createModelByName("event");


                $values = $eventModel->getZohoData();


            } else {

                //  $values = array($currentModel->getTableName() => $currentModel->DbCachedSelect());

                //     die("ok");

                // strict mode corrections

                $dbvalues = $currentModel->DbCachedSelect();

                foreach ($dbvalues as $rowkey => $rowvalues) {

                    foreach ($currentModel->getDbModel() as $key => $entry) {

                        if ($entry->display == "datepicker" && $rowvalues[$key] == "0000-00-00") {
                            $dbvalues[$rowkey][$key] = "1970-01-01";
                        }

                        if ($entry->display == "datetimepicker" && $rowvalues[$key] == "0000-00-00 00:00:00") {
                            $dbvalues[$rowkey][$key] = "1970-01-01 00:00:00";
                        }

                    }

                }

                $values[$currentModel->getTableName()] = $dbvalues;

            }


            // $values[$currentModel->getTableName()] = array();
            /*
            $entries_temp = $currentModel->GetEntries();



            $keyname = $currentModel->tryGetKeyName();

            $valuesrow = array();

            foreach($entries_temp as $key=>$entry_temp){

            $componentinfo = bpfw_getComponentHandler()->getComponent($entry_temp->display);

            $kvp = $entry_temp->GetKeyValueArray(false);

            var_dump($kvp);

            $keyval = $kvp[$keyname];
            $valuesrow[$keyval] = $kvp;

            }

            $values[$currentModel->getTableName()] = $valuesrow;

            }*/

            //  $values = array($currentModel->getTableName() => $currentModel->DbCachedSelect());


            // var_dump($values);

            if ($command == "csv2" || $command == "csv" || $command == "zohocsveventexport" || $command == "xls" || $command == "userdata" || $command == "customerdata" || $command == "eventtemplatenumberdata") {

                $csvvalues[0] = array();

                if (empty($csvvalues["header"])) {

                    foreach (current(current($values)) as $key => $v) {
                        $csvvalues["header"][$key] = $key;
                    }

                }

                foreach (current($values) as $key => $row) {
                    $csvvalues[$key] = array();

                    foreach ($row as $rk => $rv) {

                        $csvvalues[$key][$rk] = $rv;

                    }

                }

                $fp = fopen('php://output', 'w');

                if ($command == "csv2" || $command == "csv" || $command = "zohocsveventexport") {
                    foreach ($csvvalues as $csvvalue) {
                        @fputcsv($fp, $csvvalue);
                    }
                } else {


                    fputs($fp, '<html lang="de"><head><title>Export</title><meta charset="UTF-8" /></head><body>');
                    fputs($fp, "<table>");


                    $cities = array();
                    $eventtemplates = array();

                    $firstrow = true;


                    foreach ($csvvalues as $values) {

                        fputs($fp, "<tr>");

                        foreach ($values as $k => $value) {


                            if ($command == "eventtemplatenumberdata") {

                                require_once(APP_MVC_PATH . "models/cityModel.inc.php");

                                if (empty($cities)) {
                                    $citymodel = new CityModel();
                                    $cities = $citymodel->dbSelectAllAndCreateObjectArray(" 1 ");
                                }

                                if (empty($eventtemplatenames)) {

                                    $eventtemplatemodel = new EventtemplateModel();
                                    $eventtemplates = $eventtemplatemodel->dbSelectAllAndCreateObjectArray(" 1 ");

                                }

                                if ($k == "eventTemplateNumberId") continue;

                                if ($k == "cityId") {


                                    if (!empty($cities[$value]))
                                        $value = $cities[$value]->city;

                                    if ($value == $k) {
                                        $value = "Stadt";
                                    }

                                }

                                if ($k == "eventTemplateId") {

                                    if (!empty($eventtemplates[$value]))
                                        $value = $eventtemplates[$value]->eventTemplateName;

                                    if ($value == $k) {
                                        $value = "Templatename";
                                    }
                                }


                                if ($k == "eventNo") {


                                    if ($value == $k) {
                                        $value = "EventNr";
                                    }

                                }

                            }


                            if ($command == "userdata" || $command == "customerdata") {
                                if ($k != "salutation" &&
                                    $k != "firstname" &&
                                    $k != "lastname" &&
                                    $k != "email") continue;
                            }

                            fputs($fp, "<td>");
                            fputs($fp, $value);
                            fputs($fp, "</td>");

                        }

                        fputs($fp, "</tr>");

                        $firstrow = false;

                    }

                    fputs($fp, "</table>");

                    fputs($fp, '</body></html>');

                }
                fclose($fp);


            } else if ($command == "json") {

                //  header('Content-disposition: attachment; filename=file.json');
                //            header('Content-type: application/json');


                //echo json_encode(array($key=>$values[$key]));
                //$values=array();


                if ($wrapJsonInData) {
                    $jsonarray = array("data" => $values);
                } else {
                    $jsonarray = $values;
                }

            }


        }


        if ($command == "json") {

            //  header('Content-disposition: attachment; filename=file.json');
            //            header('Content-type: application/json');
            if (empty($jsonarray) || $wrapJsonInData && empty($jsonarray["data"])) {
                ob_clean();
                return null;
            } else {
                echo json_encode($jsonarray, JSON_PRETTY_PRINT);
            }

        }

        return ob_get_clean();

    }

}