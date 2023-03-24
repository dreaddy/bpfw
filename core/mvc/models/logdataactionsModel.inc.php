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

require_once(BPFW_MVC_PATH . "bpfwModelFormField.inc.php");

class LogdataactionsModel extends BpfwModel
{

    public static $instance = null;

    function __construct()
    {

        parent::__construct();

        $this->showdata = true;

        $this->minUserrankForEdit = USERTYPE_CONSULTANT;
        $this->minUserrankForShow = USERTYPE_CONSULTANT;

        $this->ignoreOnImportExport = LOG_DATAACTIONS_TABLE;

    }

    public static function removeLogEntryByRowkey($key)
    {
        if (!LOG_DATAACTIONS_TABLE) return;
        LogdataactionsModel::getInstance()->DbDeleteByWhere(" rowkey = $key");
    }

    public static function getInstance()
    {

        if (empty(LogdataactionsModel::$instance)) {
            LogdataactionsModel::$instance = new LogdataactionsModel();
        }

        return LogdataactionsModel::$instance;

    }

    /**
     * @throws Exception
     */
    public static function logNewEntry($modelname, $key, $parentkey, $shopId, $logtype = "delete", $metadata = "")
    {

        if (!LOG_DATAACTIONS_TABLE) return null;

        $model = bpfw_createModelByName($modelname);

        $values = $model->GetEntry($key);
        $keyname = $model->tryGetKeyName();

        $externalId = ($values->external_id ?? -1);

        if (method_exists($model, "getExternalId")) {
            $externalId = $model->getExternalId($key, $values);
        }

        $vals = array(
            "rowkey" => $key,
            "parentkey" => $parentkey,
            "model" => $modelname,
            "keyname" => $keyname,
            "logtype" => $logtype,
            "shopId" => $shopId,
            "external_id" => $externalId,
            "metadata" => $metadata,
            "bpfw_created" => bpfw_current_mysql_datetimestring(),
            "bpfw_modified" => bpfw_current_mysql_datetimestring()
        );

        //  $values = array("fieldname"=>$fieldname, "dialogid"=>$dialogid, "layer0identifier"=>$layer0identifier, "model"=>$tempmodel, "targetmodel"=>$targetmodel, "filtervalue"=>$filterValue, "timestamp"=>time(), "parentid"=>$parentid, "rowkey"=>$rowkey);

        return LogdataactionsModel::getInstance()->DbInsert($vals, null, true, false);

    }

    /**
     * gibt Tabellennamen zurück.
     * @return string
     */
    public function GetTableName(): string
    {
        return "logdataactions";
    }

    public function GetTitle(): string
    {
        return "Log Data Actions";
    }

    /**
     * @throws Exception
     */
    function removeLogEntryByKey($modelname, $rowkey): int
    {

        if (empty($rowkey) || $rowkey == -1) {
            return 0;
        }

        return $this->DbDeleteByWhere(" model = '$modelname' and rowkey = '$rowkey' ");

    }

    /**
     * gibt Db Model zurück. Darin beschrieben sind Typ und Name und einige Details der in der Datenbank vorkommenden Felder
     * @return array
     * @throws Exception
     */
    protected function loadDbModel(): array
    {

        if (empty($this->dbModel)) {

            $this->addPrimaryKey("modellistId");

            $this->addHiddenField("shopId", "Erstellt von Shop", BpfwDbFieldType::TYPE_INT, -1);

            $this->addField("rowkey", "rowkey", BpfwDbFieldType::TYPE_STRING, "text", array("required" => true));
            $this->addField("parentkey", "parentkey", BpfwDbFieldType::TYPE_STRING, "text", array("required" => false));


            $this->addTextField("model", "Model");
            $this->addTextField("keyname", "Keyname");


            $this->addTextField("logtype", "logtype");

            $this->addTextField("metadata", "metadata", 9999);

            $this->addField("external_id", "external_id", BpfwDbFieldType::TYPE_INT, "text", array("required" => false));

            if (!SAVE_CREATED_MODIFIED) {
                $this->addTimestamp("bpfw_created", "Created (local)", array(VIEWSETTING::DEFAULTVALUE => "NOW()", FORMSETTING::PAGE => 1, FORMSETTING::DISABLED => true, LISTSETTING::HIDDENONLIST => true, FORMSETTING::HIDDENONADD => true, FORMSETTING::POSITION => POSITION_RIGHT));
                $this->addTimestamp("bpfw_modified", "Last modified (local)", array(FORMSETTING::PAGE => 1, FORMSETTING::DISABLED => true, LISTSETTING::HIDDENONLIST => true, FORMSETTING::HIDDENONADD => true, FORMSETTING::POSITION => POSITION_RIGHT));
            }

        }

        return $this->dbModel;

    }

}
