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



require_once(BPFW_MVC_PATH . "bpfwModelFormField.inc.php");

class TempmodellistModel extends BpfwModel
{


    public static $instance = null;

    function __construct($values = null, $autocompleteVariables = true)
    {
        parent::__construct($values, $autocompleteVariables);

        $this->showdata = true;

        $this->minUserrankForEdit = USERTYPE_CONSULTANT;
        $this->minUserrankForShow = USERTYPE_CONSULTANT;

        $this->ignoreOnImportExport = true;

    }

    public static function getNewEntry($fieldname, $dialogid, $tempmodel, $targetmodel, $filterValue, $layer0identifier, $parentid, $rowkey)
    {


        $values = array("fieldname" => $fieldname, "dialogid" => $dialogid, "layer0identifier" => $layer0identifier, "model" => $tempmodel, "targetmodel" => $targetmodel, "filtervalue" => $filterValue, "timestamp" => time(), "parentid" => $parentid, "rowkey" => $rowkey);
        return TempmodellistModel::getInstance()->DbInsert($values);

    }

    public static function getInstance()
    {

        if (empty(TempmodellistModel::$instance)) {
            TempmodellistModel::$instance = new TempmodellistModel();
        }

        return TempmodellistModel::$instance;

    }

    /**
     * gibt Tabellennamen zurück.
     * @return string
     */
    public function GetTableName(): string
    {
        return "tempmodellist";
    }

    public function GetTitle(): string
    {
        return "temp modellist cache";
    }

    /**
     * gibt Db Model zurück. Darin beschrieben sind Typ und Name und einige Details der in der Datenbank vorkommenden Felder
     * @return array
     * @throws Exception
     * @throws Exception
     * @throws Exception
     * @throws Exception
     * @throws Exception
     * @throws Exception
     */
    protected function loadDbModel(): array
    {

        if (empty($this->dbModel)) {

            $this->addPrimaryKey("modellistId");

            $this->addField("dialogid", "Dialogid", BpfwDbFieldType::TYPE_STRING, "text", array("required" => true));
            $this->addField("layer0identifier", "Dialogid Layer 0", BpfwDbFieldType::TYPE_STRING, "text", array("required" => true));
            $this->addField("parentid", "parentid", BpfwDbFieldType::TYPE_INT, "text", array("required" => true));
            $this->addField("rowkey", "rowkey", BpfwDbFieldType::TYPE_INT, "text", array("required" => true));
            $this->addTextField("fieldname", "Fieldname");
            $this->addTextField("model", "Model");
            $this->addTextField("targetmodel", "Model");
            $this->addTextField("filtervalue", "Filtervalue");
            $this->addTextFieldNumeric("timestamp", "timestamp");


        }

        return $this->dbModel;


    }


}
