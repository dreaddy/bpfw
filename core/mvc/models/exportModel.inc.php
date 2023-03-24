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

class ExportModel extends BpfwModel
{

    var bool $translateDbModelLabels=true;
    function __construct($values = null, $autocompleteVariables = true)
    {

        parent::__construct($values, $autocompleteVariables);

        $this->minUserrankForEdit = USERTYPE_INVALID;
        $this->minUserrankForShow = USERTYPE_ADMIN;
        $this->minUserrankForAdd = USERTYPE_INVALID;
        $this->minUserrankForDelete = USERTYPE_INVALID;
        $this->minUserrankForDuplicate = USERTYPE_INVALID;

        $this->ignoreOnSync = true;
        $this->showdata = true;
    }

    /**
     * @throws Exception
     */
    public function DbCachedSelect(string $where = " 1", int $count = -1, int $offset = 0, array $sort = array(), array $extrafields = array(), string $join = "", bool $temptable = false): array
    {

        $dbdata = $this->getAllDbModelsWithTable();

        $retval = array();

        foreach ($dbdata as $table => $info) {
            $model = bpfw_createModelByName($table);
            if (!$model->ignoreOnImportExport) {
                $retval[$table] = array("tableID" => $table);
            }

        }

        return $retval;

    }

    /**
     * gibt Tabellennamen zurück.
     * @return string
     */
    public function GetTableName(): string
    {
        return "export";
    }

    public function GetTitle(): string
    {
        return __("Export - Hint: Attachments will not be exported. Use 'Backup' to do that");
    }

    /**
     * gibt Db Model zurück. Darin beschrieben sind Typ und Name und einige Details der in der Datenbank vorkommenden Felder
     * @return array
     * @throws Exception
     */
    protected function loadDbModel(): array
    {

        $this->addPrimaryKey("tableID", "Table name", BpfwDbFieldType::TYPE_INT, 'text', array(LISTSETTING::HIDDENONLIST => false));
        $this->addCheckbox("delete Data", "export", array(VIEWSETTING::DEFAULTVALUE => 1));

        return $this->dbModel;
    }

}

/*
function executeModelHandler($model){

    global $database;

    $model->showdata = false;

    $model->subtitle = "Pdf Generator / Mail sender";

    $model->minUserrankForEdit = USERTYPE_CONSULTANT;
    $model->minUserrankForShow = USERTYPE_CONSULTANT;
    $model->minUserrankForAdd = USERTYPE_CONSULTANT;

}*/