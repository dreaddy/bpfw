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

class DatalogModel extends BpfwModel
{

    public static $instance = null;


    var bool $translateDbModelLabels = true;
    function __construct()
    {

        parent::__construct();

        $this->showdata = true;

        $this->minUserrankForEdit = USERTYPE_CONSULTANT;
        $this->minUserrankForShow = USERTYPE_CONSULTANT;

        $this->ignoreOnImportExport = true; // never export or backup

    }

    /**
     * Summary of addLogEntry
     * @param string $modelname
     * @param string|int $key
     * @param mixed $logtype
     * @param mixed $metadata
     * @return int
     */
    public static function addLogEntry(string $modelname, string|int $key, string $logtype = "delete", string $metadata = "{{{readEntries}}}"): int
    {


        if (!LOG_CHANGES) return -1;


        try {

            $model = bpfw_createModelByName($modelname);

            $keyname = $model->tryGetKeyName();

            $sql = "-";

            if ($metadata == "{{{readEntries}}}") {

                $db = bpfw_getDb();
                $keyesc = bpfw_getDb()->escape_string($key);
                $sql = "select * from `$modelname` where $keyname = '$keyesc'";
                $values = $db->makeSelect($sql);
                $metadata = json_encode($values);

            }

            if (strlen($metadata) > 9999) {
                $metadata = substr($metadata, 0, 9998);
            }


            $vals = array(
                "rowkey" => $key,
                "bpfwmodel" => $modelname,
                "keyname" => $keyname,
                "logtype" => $logtype,
                "metadata" => $metadata,
                "userId" => bpfw_getUserId(),
                "bpfw_created" => bpfw_current_mysql_datetimestring(),
                "bpfw_modified" => bpfw_current_mysql_datetimestring()
            );

            return DatalogModel::getInstance()->DbInsert($vals, null, true, false);

        } catch (Exception $ex) {

            // no exception output to prevent deadlock when tables do not exist for example
            return -1;
        }

    }

    public static function getInstance()
    {

        if (empty(DatalogModel::$instance)) {
            DatalogModel::$instance = new DatalogModel();
        }

        return DatalogModel::$instance;

    }

    /**
     * gibt Tabellennamen zurück.
     * @return string
     */
    public function GetTableName(): string
    {
        return "datalog";
    }

    public function GetTitle(): string
    {
        return __("Crudaction log");
    }

    /*
    public static function removeLogEntryByRowkey($key){

    if(!LOG_CHANGES)return;

        DatalogModel::getInstance()->DbDeleteByWhere(" rowkey = $key");


    }*/

    /**
     * gibt Db Model zurück. Darin beschrieben sind Typ und Name und einige Details der in der Datenbank vorkommenden Felder
     * @return array
     * @throws Exception
     * @throws Exception
     */
    protected function loadDbModel(): array
    {

        if (empty($this->dbModel)) {

            $this->addPrimaryKey("datalogId");


            $this->addField("rowkey", "Row key", BpfwDbFieldType::TYPE_STRING, "text", array("required" => false));


            $this->addTextField("bpfwmodel", "Model");
            $this->addTextField("keyname", "Keyname");


            $this->addTextField("logtype", "Logtype");

            $this->addTextField("metadata", "Metadata", 9999);

            $this->addTextField("userId", "Executed by userId");

            if (!SAVE_CREATED_MODIFIED) {
                $this->addTimestamp("bpfw_created", "Created (local)", array(VIEWSETTING::DEFAULTVALUE => "NOW()", FORMSETTING::PAGE => 1, FORMSETTING::DISABLED => true, LISTSETTING::HIDDENONLIST => true, FORMSETTING::HIDDENONADD => true, FORMSETTING::POSITION => POSITION_RIGHT));
                $this->addTimestamp("bpfw_modified", "Last edited (local)", array(FORMSETTING::PAGE => 1, FORMSETTING::DISABLED => true, LISTSETTING::HIDDENONLIST => true, FORMSETTING::HIDDENONADD => true, FORMSETTING::POSITION => POSITION_RIGHT));
            }


        }

        return $this->dbModel;

    }


    /*
    function removeLogEntryByKey($modelname, $rowkey){

        if(empty($rowkey) || $rowkey == -1){
            return 0;
        }

        $deletedEntrycount = $this->DbDeleteByWhere( " model = '$modelname' and rowkey = '$rowkey' ");

        return $deletedEntrycount;

    }*/

}
