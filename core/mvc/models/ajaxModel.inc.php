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

class AjaxModel extends BpfwModel
{

    function __construct($values = null, $autocompleteVariables = true)
    {
        parent::__construct($values, $autocompleteVariables);

        $this->minUserrankForEdit = USERTYPE_ADMIN;
        $this->minUserrankForShow = USERTYPE_GUEST;
        $this->minUserrankForAdd = USERTYPE_ADMIN;

        $this->showdata = false;

    }

    /**
     * gibt Tabellennamen zur�ck.
     * @return string
     */
    public function GetTableName(): string
    {
        return "ajax";
    }

    public function GetTitle(): string
    {
        return "Common Ajax Commands";
    }

    /**
     * gibt Db Model zur�ck. Darin beschrieben sind Typ und Name und einige Details der in der Datenbank vorkommenden Felder
     * @return array
     */
    protected function loadDbModel(): array
    {
        return array();
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