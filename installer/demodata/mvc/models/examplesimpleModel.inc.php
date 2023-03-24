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

class ExamplesimpleModel extends BpfwModel
{
    // enable if you want all the labels in loadDbModel to be translated
    // var bool $translateDbModelLabels = true;

    function __construct($values = null, $autocompleteVariables = true)
    {
        parent::__construct($values, $autocompleteVariables);
    }

    /**
     * gibt Tabellennamen zurück.
     * @return string
     */
    public function GetTableName(): string
    {
        return "examplesimple";
    }

    public function GetTitle(): string
    {
        return "Simple Example found in examplesimpleModel.inc.php";
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function loadDbModel(): array
    {

        if (empty($this->dbModel)) {

            $this->addPrimaryKey("examplesimpleId");

            $this->addComboBox("combobox", "Combobox", array(1=>"first val", 2=>"second val"));
            $this->addTextField("test", "Text");
            $this->addTextFieldNumeric("number", "Number");
            $this->addCheckbox("checkbox", "Checkbox");
            $this->addTinyMceHtmlEditor("tinymce", "Tinymce Editor", array(FORMSETTING::POSITION => POSITION_RIGHT, "hiddenOnList" => true));

            // $this->addField("userlisttest", "Berater", BpfwDbFieldType::TYPE_IGNORE, "modellist", array(VIEWSETTING::DATA_MODEL_FIELDS => array("firstname", "lastname"), VIEWSETTING::DATA_MODEL => "user", VIEWSETTING::DATA_FILTER => "formid", FORMSETTING::POSITION => POSITION_RIGHT, "hiddenOnList" => true, FORMSETTING::SHOWLABEL_IN_FORM => true));

        }

        return $this->dbModel;

    }

}
