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


class EmailattachmentsModel extends BpfwModel
{

    var bool $translateDbModelLabels = true;
    function __construct()
    {


        $this->minUserrankForAdd = USERTYPE_ADMIN;
        $this->minUserrankForShow = USERTYPE_ADMIN;

        $this->pagesAdd = 1;
        $this->pagesEdit = 1;

        $this->sortColumn = 2;
        $this->sortOrder = "desc";

        parent::__construct();

    }

    /**
     * gibt Tabellennamen zurück.
     * @return string
     */
    public function GetTableName(): string
    {
        return "emailattachments";
    }

    public function GetTitle(): string
    {

        return __("E-mail attachments");

    }

    /**
     * gibt Db Model zurück. Darin beschrieben sind Typ und Name und einige Details der in der Datenbank vorkommenden Felder
     * @return array
     * @throws Exception
     */
    protected function loadDbModel(): array
    {

        global $database;

        if (empty($this->dbModel)) {


            $category = array("All"=>"All");
            if (function_exists("bpfw_getPdfCategoriesAsStringStringArray")) {
                $category = bpfw_getPdfCategoriesAsStringStringArray();
            }

            $this->addPrimaryKey("emailattachmentId");
            $this->addComboBoxMultiselect("category", "Category", new EnumHandlerArray($category), BpfwDbFieldType::TYPE_STRING, array(VIEWSETTING::DEFAULTVALUE=>array("All"), FORMSETTING::ADDPAGE => 1, FORMSETTING::REQUIRED => true, LISTSETTING::HIDDENONPRINT => true, FORMSETTING::HIDDENONADD => false, LISTSETTING::HIDDENONLIST => true));

            $this->addTextField("headline", "Headline in Backend", null, array(FORMSETTING::REQUIRED => true));

            $this->addTextField("attachment_name", "Attachment name", null, array(FORMSETTING::REQUIRED => false));

            $this->addCheckbox("checked", "Activated per default");

            $this->addFileField("file", "File", true, array(FORMSETTING::REQUIRED => true));


        }

        return $this->dbModel;


    }

}
