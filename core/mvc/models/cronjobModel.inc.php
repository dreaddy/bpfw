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


class CronjobModel extends BpfwModel
{

    var bool $translateDbModelLabels = true;
    function __construct()
    {

        parent::__construct();

        $this->showdata = true;

        $this->minUserrankForEdit = USERTYPE_ADMIN;
        $this->minUserrankForShow = USERTYPE_ADMIN;

    }

    /**
     * gibt Tabellennamen zurück.
     * @return string
     */
    public function GetTableName(): string
    {
        return "cronjob";
    }

    public function GetTitle(): string
    {
        return __("Cronjobs");
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

            $advisorEnum = array(-1 => "invalid");
            $advisorName = "not logged in";

            if (!empty(bpfw_getUser())) {

                $advisorName = bpfw_getUser()["username"];

                if (isset(bpfw_getUser()["lastname"]) && isset(bpfw_getUser()["firstname"])) {
                    $advisorName = bpfw_getUser()["lastname"] . ", " . bpfw_getUser()["firstname"];
                }

                if (bpfw_isAdmin()) {
                    $advisorEnum = new EnumHandlerDb("user", "userId", 'CONCAT_WS(", ",lastname,firstname)', '', true);
                } else {
                    $advisorEnum = new EnumHandlerArray(array(bpfw_getUserId() => "$advisorName"));
                }

            }

            $this->addPrimaryKey("cronjobId");

            $this->addComboBox("frequency", "Frequency", new EnumHandlerCallback("bpfw_getCronjobtimesArray"), BpfwDbFieldType::TYPE_STRING, array("hiddenOnList" => false));
            $this->addComboBox("type", "Type", new EnumHandlerCallback("bpfw_getCronjobtasksArray"), BpfwDbFieldType::TYPE_STRING, array("hiddenOnList" => false));
            $this->addCheckbox("js_execution", "Execute with JS calls, too", array("hiddenOnList" => false, VIEWSETTING::DEFAULTVALUE => 1));
            $this->addComboBox("creatorUserId", "Created By", $advisorEnum, BpfwDbFieldType::TYPE_INT, array(LISTSETTING::HIDDENONLIST => false, VIEWSETTING::DEFAULTVALUE => bpfw_getUserId()));
            $this->addTimestamp("lastExecuted", "Last execution", array(FORMSETTING::HIDDENONADD => true, FORMSETTING::HIDDENONEDIT => true, VIEWSETTING::DEFAULTVALUE => null));
            $this->addTinyMceHtmlEditor("lastExecutionMessage", "Log last run", array(FORMSETTING::HIDDENONADD => true, FORMSETTING::HIDDENONEDIT => true, VIEWSETTING::DEFAULTVALUE => null));

        }

        return $this->dbModel;


    }


}
