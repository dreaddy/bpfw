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

// use UI\Controls\Button;

class CreatesearchindexModel extends BpfwEmptyModel
{

    function __construct($values = null, $autocompleteVariables = true)
    {
        parent::__construct();

        $this->minUserrankForShow = USERTYPE_ADMIN;
        $this->showdata = false;
        $this->showNavigation = true;

    }

    /**
     * @throws Exception
     */
    function listSearchindexTables()
    {


        // $dbdata = $this->getDbData();

        $modelobjects = dbModel::getAllModels();

        foreach ($modelobjects as $tablename => $modelobject) {


            if ($modelobject->createSearchIndex) {

                echo "<div class='searchindexwrapper wrapper-$tablename'>$tablename:";

                $status = $this->getSearchindexStatus($tablename, $modelobject);

                echo " <div class='amount_done'>" . ($status["count"] - $status["noindex"]) . "</div>/<div class='count'>" . $status["count"] . "</div> with Index";

                echo "<button style='margin-left:10px' class='clearButton' data-model='" . $tablename . "'>clear</button>";

                echo "<button style='margin-left:10px' class='createButton' data-model='" . $tablename . "'>create</button>";


                echo "</div>";

            }
        }

        // var_dump($modeldata);


    }

    /**
     * Summary of getSearchindexStatus
     * @param string $tablename
     * @param BpfwModel $modelobject
     * @return array
     * @throws Exception
     */
    function getSearchindexStatus(string $tablename, BpfwModel $modelobject): array
    {

        $db = bpfw_getDb();

        $retval = array();

        $retval["count"] = $db->countTableEntries($tablename, $modelobject->tryGetKeyName());

        $retval["noindex"] = $db->countTableEntries($tablename, $modelobject->tryGetKeyName(), " searchindex = '' or searchindex is null ");

        return $retval;

    }

    /**
     * Summary of getSlug
     * @return string
     */
    public function getSlug(): string
    {
        return "createsearchindex";
    }

    public function getTitle(): string
    {

        return __("Searchindex");

    }

}
