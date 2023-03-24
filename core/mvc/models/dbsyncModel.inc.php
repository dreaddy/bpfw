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

class DbsyncModel extends BpfwEmptyModel
{

    function __construct()
    {
        parent::__construct();

        $this->minUserrankForShow = USERTYPE_ADMIN;
        $this->showdata = false;
        $this->showNavigation = true;

    }





    /*
     * object(BpfwModelFormfield)#289 (25) {
    ["autocomplete"]=>
    NULL
    ["condition"]=>
    NULL
    ["addpage"]=>
    int(-1)
    ["editpage"]=>
    int(-1)
    ["position"]=>
    string(4) "left"
    ["xtraFormClass"]=>
    string(0) ""
    ["data"]=>
    array(0) {
    }
    ["labelyes"]=>
    string(2) "Ja"
    ["labelno"]=>
    string(4) "Nein"
    ["showLabel"]=>
    bool(false)
    ["parentComponent"]=>
    NULL
    ["name"]=>
    string(6) "cityId"
    ["label"]=>
    string(2) "ID"
    ["display"]=>
    string(6) "hidden"
    ["type"]=>
    string(3) "int"
    ["default"]=>
    string(0) ""
    ["entries"]=>
    array(0) {
    }
    ["primaryKey"]=>
    bool(true)
    ["hiddenOnPrint"]=>
    bool(false)
    ["disabled"]=>
    bool(false)
    ["hiddenOnList"]=>
    bool(false)
    ["hiddenOnEdit"]=>
    bool(false)
    ["hiddenOnAdd"]=>
    bool(false)
    ["doNotEdit"]=>
    bool(false)
    ["required"]=>
    bool(false)
    }
    */

    /**
     * @throws Exception
     */
    function listSearchindexTables()
    {


        $dbdata = $this->getDbData();

        $modelobjects = dbModel::getAllModels();

        foreach ($modelobjects as $tablename => $modelobject) {

            $modeldata = $modelobject->getDbModel();
            if (empty($modeldata)) {
                // echo "skipping $keyname<br>";
                continue; // no db for this model, skip
            }


            //$dbdata = $this->getDbData();

            if (!$modelobject->ignoreOnSync) {

                $temptablename = $modelobject->GetTempTableName();

                if (!isset($dbdata[$tablename])) {

                    echo "no Table found for $tablename ";
                    echo " <a class='modify_db_link' href='?p=dbsync&action=createTable&table=$tablename'>create Table</a>";
                    echo "<br>";

                } else {

                    $this->matchDbWithModel($tablename, $dbdata[$tablename], $modelobject);

                    if (!empty($dbdata[$temptablename])) {

                        $this->matchDbWithModel($temptablename, $dbdata[$temptablename], $modelobject, true);

                    }

                }

            }

        }

        // var_dump($modeldata);


    }

    /**
     * Summary of matchDbWithModel
     * @param string $name
     * @param array[] $dbdata
     * @param BpfwModel $modelobject
     * @param bool $isTempTable
     * @throws Exception
     */
    function matchDbWithModel(string $name, array $dbdata, BpfwModel $modelobject, bool $isTempTable = false)
    {

        $keyname_model = $modelobject->tryGetKeyName();

        if (!$isTempTable) {
            echo "<b>checking $name</b>";
        } else {
            echo "<b>checking temptable of " . $modelobject->GetTableName() . " ($name)</b>";
        }

        $modeldata = $modelobject->getDbModel();

        foreach ($dbdata as $key => $entry) {

            if (($entry["Key"] != "PRI")) {
                if (!empty($modeldata[$key])) {
                    // echo "DB $name: unused field in db: ".$key."<br>";
                } else {
                    echo "DB $name: unused field in db: " . $key;
                    echo " <a class='modify_db_link' href='?p=dbsync&action=delete&table=$name&field=$key'>delete</a>";

                }
            } else {

                if ($key != $keyname_model) {
                    echo "Key in table is $key and should be $keyname_model:";
                    echo " <a class='modify_db_link' href='?p=dbsync&action=rename_pk&table=$name&oldname=$key&newname=$keyname_model&sqltype={$entry["Type"]}&default=" . urlencode($entry["Default"]) . "'>rename</a>";
                }


            }

        }

        echo "<br>";


        foreach ($modeldata as $key => $entry) {


            if ($entry->type->type != BpfwDbFieldType::TYPE_IGNORE && $entry->type->type != BpfwDbFieldType::TYPE_FOREIGN) {

                $defaultvalue =
                    $entry->getDefaultForMysql();

                if (!empty($dbdata[$entry->name])) {

                    $dbfield = $dbdata[$entry->name];

                    $dbtypeLength = "default";
                    if (strpos($dbfield["Type"], "(")) {
                        $dbbasetype = mb_substr($dbfield["Type"], 0, strpos($dbfield["Type"], "("));
                        $dbtypeLength = mb_substr($dbfield["Type"], strpos($dbfield["Type"], "(") + 1, strpos($dbfield["Type"], ")") - strpos($dbfield["Type"], "(") - 1);
                    } else {
                        $dbbasetype = $dbfield["Type"];
                    }

                    if ($entry->type->type != $dbbasetype) {
                        echo "DB $name: " . $entry->name . " type not matching. is:" . $dbbasetype . "(" . $dbtypeLength . ")" . " - should:" . $entry->type->type . "(" . $entry->type->length . ")";

                        echo " <a class='modify_db_link' href='?p=dbsync&action=changeType&table=$name&default=" . urlencode($defaultvalue) . "&field=" . $entry->name . "&oldtype=$dbbasetype&newtype=" . $entry->type->type . "&newlength=" . $entry->type->length . "'>Change type</a>";
                        echo "<br>";
                    } else {

                        if ($dbtypeLength != $entry->type->length && $entry->type->length != "default") {
                            echo "DB $name: " . $entry->name . " length not matching. is:" . $dbbasetype . "(" . $dbtypeLength . ")" . " - should:" . $entry->type->type . "(" . $entry->type->length . ")";
                            echo " <a class='modify_db_link' href='?p=dbsync&action=changeSize&table=$name&default=" . urlencode($defaultvalue) . "&field=" . $entry->name . "&oldtype=$dbbasetype&newtype=" . $entry->type->type . "&newlength=" . $entry->type->length . "'>Change size</a>";
                            echo "<br>";
                        }

                    }


                } else {

                    // link table
                    if ($entry->type->type == BpfwDbFieldType::TYPE_LINK_TABLE) {

                        $linktablename = $modelobject->getLinkTableName($entry->name);

                        $dbdatainfo = $this->getDbData();

                        if (isset($dbdatainfo[$linktablename])) {

                            $i = 1;
                            $pk = "";
                            $key1 = "";
                            $key2 = "";

                            foreach ($dbdatainfo[$linktablename] as $fieldname => $values) {

                                switch ($i) {
                                    case 1:
                                        $pk = $fieldname;
                                        break;
                                    case 2:
                                        $key1 = $fieldname;
                                        break;
                                    case 3:
                                        $key2 = $fieldname;
                                        break;
                                }


                                $i++;

                            }

                            if (empty($key2) || $key1 == $key2 || $key2 != $entry->linktable_valuename && !empty($entry->linktable_valuename)) {

                                $newname = $entry->linktable_valuename;
                                if (empty($newname)) $newname = $key2;

                                if (empty($newname) || $newname == $key2) {
                                    echo "<div style='color:red;font-weight:bold'>DB $name: Linktable keys are identical ( $key1==$key2 ). Set linktable_valuename $entry->linktable_valuename : $linktablename (" . $entry->name . ")</div>";
                                } else {
                                    if (empty($key2)) {
                                        echo "DB $name: key2 missing: $linktablename (" . $entry->name . ")";
                                        echo " <a class='modify_db_link' href='?p=dbsync&action=createLinktablefield&table=$name&linktable_field=" . $newname . "&default=" . urlencode($defaultvalue) . "&field=" . $entry->name . "&newtype=" . $entry->type->type . "&newlength=" . $entry->type->length . "'>Create Link Table Key2</a>";
                                    } else {
                                        echo "DB $name: key2 needs rename from $key2 to $newname: $linktablename (" . $entry->name . ")";
                                        echo " <a class='modify_db_link' href='?p=dbsync&action=renameLinktablefield&table=$name&linktable_field=" . $key2 . "&linktable_oldname=$newname&default=" . urlencode($defaultvalue) . "&field=" . $entry->name . "&newtype=" . $entry->type->type . "&newlength=" . $entry->type->length . "'>Rename Link Table Key2</a>";
                                    }
                                    echo "<br>";
                                }
                            }


                        } else {


                            echo "DB $name: missing link table in db: $linktablename (" . $entry->name . ")";
                            echo " <a class='modify_db_link' href='?p=dbsync&action=createField&table=$name&default=" . urlencode($defaultvalue) . "&field=" . $entry->name . "&newtype=" . $entry->type->type . "&newlength=" . $entry->type->length . "'>Create Link Table</a>";
                            echo "<br>";

                        }

                    } else {
                        if (!$entry->primaryKey) {
                            echo "DB $name: missing field in db: " . $entry->name . " default " . $defaultvalue;
                            echo " <a class='modify_db_link' href='?p=dbsync&action=createField&table=$name&default=" . urlencode($defaultvalue) . "&field=" . $entry->name . "&newtype=" . $entry->type->type . "&newlength=" . $entry->type->length . "'>Create Field</a>";
                            echo "<br>";
                        }

                    }

                }

            }

        }


        // echo "<hr>";

    }

    /**
     * Summary of getSlug
     * @return string
     */
    public function getSlug(): string
    {
        return "dbsync";
    }

    public function getTitle(): string
    {

        return __("Sync database <-> models");

    }

}
