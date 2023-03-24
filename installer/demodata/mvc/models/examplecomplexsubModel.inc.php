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
require_once(BPFW_MVC_PATH."bpfwModelFormField.inc.php");

class ExamplecomplexsubModel extends BpfwModel
{
    // enable if you want all the labels in loadDbModel to be translated
    // var bool $translateDbModelLabels = true;

    // stores the current filter for the entries
    // can also be "formid" to use value of parent form, so has to be string
    private $examplecomplexId;

    function __construct($examplecomplexId = null){

        $this->examplecomplexId = $examplecomplexId;

        // autoadds a FILTER_ENTRY_SELECT_WHERE filter
        $this->filteredField = "examplecomplexId";
        $this->filteredModel = "examplecomplex";

        $filter = getorpost("filter");
        if(!empty($filter) && $this->examplecomplexId === null){
            $this->examplecomplexId = getorpost("filter");
        }

        $this->showdata = true;
        $this->minUserrankForEdit = USERTYPE_ADMIN;
        $this->minUserrankForShow = USERTYPE_CONSULTANT;
        $this->minUserrankForAdd = USERTYPE_ADMIN;
        $this->sortColumn = 1;
        $this->sortOrder = "desc";

        parent::__construct();

    }


    /**
     * tablename
     * @return string
     */
    public function GetTableName(): string
    {
        return "examplecomplexsub";
    }

    public function GetTitle(): string
    {
        return "Submodel of examplecomplex";
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function loadDbModel(): array
    {
        if (empty($this->dbModel)) {

            $this->addPrimaryKey("examplecomplexsubId");
            // this will automatically add the Filtervalue of the parent to the database
            $this->addHiddenField("examplecomplexId", "examplecomplexId", BpfwDbFieldType::TYPE_INT, $this->examplecomplexId, array());
            $this->addTextField("firstname", "Firstname");
            $this->addTextField("lastname", "Lastname");
            $this->addTextFieldNumeric("anothervalue", "Another value");

        }

        return $this->dbModel;

    }

}
