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
class ImportControl extends DefaultControl
{


    function handleAjaxCommand(string $command): void
    {


        global $database;
        $model = $this->model;

        $allModels = $model->getAllModelsWithTable();

        if ($_GET['model'] != "all" && !empty($allModels[$_GET['model']])) {
            $currentModels[$_GET['model']] = $allModels[$_GET['model']];
        } else if ($_GET['model'] == "all") {
            $currentModels = $allModels;
        } else {
            throw new Exception("Model not Existing");
        }

        $csvValues = array();


        if ($command == "json") {

            echo "folgende Daten gefunden; xyxy";
            echo "Bestandsdaten l�schen oder zusammenf�hren?";

        } else {
            parent::handleAjaxCommand($command);
            die();
        }


        foreach ($currentModels as $key => $currentModel) {

            $values = array($currentModel->getTableName() => $currentModel->DbCachedSelect());

            if ($command == "csv" || $command == "xls") {

                $csvValues[0] = array();
                foreach (current(current($values)) as $key2 => $v) {
                    $csvValues["header"][$key2] = $key2;
                }

                foreach (current($values) as $key2 => $row) {
                    $csvValues[$key2] = array();

                    foreach ($row as $rk => $rv) {
                        $csvValues[$key2][$rk] = $rv;
                    }

                }

                $fp = fopen('php://output', 'w');

                if ($command == "csv") {
                    foreach ($csvValues as $values) {
                        fputcsv($fp, $values);
                    }
                } else {
                    fputs($fp, "<table>");
                    foreach ($csvValues as $values) {
                        fputs($fp, "<tr>");
                        foreach ($values as $value) {
                            fputs($fp, "<td>");
                            fputs($fp, $value);
                            fputs($fp, "</td>");
                        }
                        fputs($fp, "</tr>");
                    }
                    fputs($fp, "</table>");
                }
                fclose($fp);


            } else if ($command == "json") {

                //  header('Content-disposition: attachment; filename=file.json');
                //            header('Content-type: application/json');


                echo json_encode($values);


            }

        }

        die();

    }

}