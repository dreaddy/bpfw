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
class EmailattachmentsControl extends DefaultControl
{


    function handleAjaxCommand(string $command): void
    {

        global $database;
        $model = $this->model;


        if ($command == "saveCheckbox" || $command == "saveGraphicalcombobox") {

            $ajaxUid = $_POST["userId"];

            $colkey = $_POST["colkey"];
            $colvalue = $_POST["colvalue"];
            $rowkey = $_POST["rowkey"];
            $rowvalue = $_POST["rowvalue"];

            $page = bpfw_getActivePage(); // event

            if (isset($colkey) &&
                isset($colvalue) && is_numeric($colvalue) &&
                isset($rowkey) &&
                isset($rowvalue) && is_numeric($rowvalue) &&
                $ajaxUid == bpfw_getUserId() &&
                bpfw_isAdmin()
            ) {

                echo "all ok, setting $colkey to $colvalue where $rowkey is $rowvalue";

                $data =

                    array(
                        $colkey => new DbSubmitValue($colkey, $colvalue, $this->model)
                        /* array(
                             "field"=>$model->getDbModel()[$colkey],
                             "data"=> $colvalue
                             )*/
                    );


                $affectedRows = $database->makeUpdate($data, $page, new DatabaseKey($rowkey, $rowvalue, 'i'));
                echo "<br>rows affected: $affectedRows";
                if ($model->error != null) {

                    echo "error:";
                    echo $model->error;

                }

            }

        } else {
            parent::handleAjaxCommand($command);
        }


    }


}