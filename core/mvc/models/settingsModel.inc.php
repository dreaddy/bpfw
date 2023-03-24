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


class SettingsModel extends BpfwModel
{

    function __construct($values = null, $autocompleteVariables = true)
    {
        parent::__construct($values, $autocompleteVariables);

        $this->minUserrankForAdd = USERTYPE_INVALID;
        $this->minUserrankForDuplicate = USERTYPE_INVALID;
        $this->minUserrankForDelete = USERTYPE_INVALID;

        $this->sortColumn = 0;
        $this->sortOrder = "asc";

        $this->ignoreOnImportExport = true;

    }

    /**
     * Summary of EditEntry
     * @param DbSubmitValue[] $data // |DbModelEntry keyvalue or data array or DbModelEntry
     * @throws Exception
     */
    public function EditEntry(array $data, bool $ignoreRightsManagement = false, bool $ignoreConversion = false, bool $temptable = false): void
    {


        $valuesSet = parent::DbCachedSelect();

        if (isset($valuesSet[$data["settings_key"]->data])) {
            parent::EditEntry($data, $ignoreRightsManagement, $ignoreConversion);
        } else {
            parent::AddEntry($data, true, $ignoreConversion);
        }

    }

    public function DbCachedSelect(string $where = " 1", int $count = -1, int $offset = 0, array $sort = array(), array $extrafields = array(), string $join = "", bool $temptable = false): array
    {

        $valuesSet = parent::DbCachedSelect($where, $count, $offset, $sort, $extrafields, $join);

        $dbdata = bpfw_getDefaultSettings();

        $retval = array();

        foreach ($dbdata as $key => $value) {

            if (isset($valuesSet[$key])) {
                $value = $valuesSet[$key]["settings_value"];
            }

            $retval[$key] = array("settings_key" => $key, "settings_value" => $value);

        }

        return $retval;

    }

    /**
     * gibt Tabellennamen zurück.
     * @return string
     */
    public function GetTableName(): string
    {
        return "settings";
    }

    public function GetTitle(): string
    {
        return "Einstellungen";
    }

    /**
     * gibt Db Model zurück. Darin beschrieben sind Typ und Name und einige Details der in der Datenbank vorkommenden Felder
     * @return array
     * @throws Exception
     * @throws Exception
     */
    protected function loadDbModel(): array
    {

        if (empty($this->dbModel)) {

            $this->addPrimaryKey("settings_key", "Schlüssel", new BpfwDbFieldType(BpfwDbFieldType::TYPE_VARCHAR, 100), "text", array(LISTSETTING::HIDDENONLIST => false));


            $options = bpfw_getPredefinedOptionsForSetting(getorpost("edit"));

            if (empty($options)) {
                $this->addTextField("settings_value", "Wert", 2000);
            } else {
                $this->addComboBox("settings_value", "Wert", $options, new BpfwDbFieldType(BpfwDbFieldType::TYPE_STRING, 2000));
            }


        }

        return $this->dbModel;

    }

}

