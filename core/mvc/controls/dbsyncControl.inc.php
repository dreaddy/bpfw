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

/**
 * defaultListView short summary.
 *
 * defaultListView description.
 *
 * @version 1.0
 * @author torst
 */
class DbsyncControl extends DefaultControl
{


    var array $commandsExecuted = array();

    function __construct($model)
    {
        parent::__construct($model);
    }

    /**
     * @throws Exception
     */
    function handleActions(): void
    {

        // TODO: irgendwie noch sicherer machen
        if (bpfw_isAdmin() && isset($_GET["action"])) {

            //  $db = bpfw_getDb();


            /*if( $_GET["newtype"] == BpfwDbFieldType::TYPE_LINK_TABLE){
                // create nm table

                echo "DB $name: ".$entry->name." needs a linking Table. is:" . $dbbasetype."(".$dbtypeLength.")"." - should:".$entry->type->type."(".$entry->type->length.")";

                echo " <a href='?p=dbsync&action=changeType&table=$name&field=".$entry->name."&newtype=".$entry->type->type."&newlength=".$entry->type->length."'>change Type</a>";
                echo "<br>";

            }else*/

            //{

            switch (getorpost("action")) {

                case "rename_pk":
                    $table = getorpost("table");
                    $newname = getorpost("newname");
                    $oldname = getorpost("oldname");
                    $default = getorpost("default");
                    $sqltype = getorpost("sqltype");
                    $this->renameField($table, $newname, $oldname, $sqltype, true, $default);
                    break;

                case "changeType":
                case "changeSize":

                    $table = getorpost("table");
                    $field = getorpost("field");
                    $newtype = getorpost("newtype");
                    $oldtype = getorpost("oldtype");
                    $default = getorpost("default");
                    $newlength = NULL;
                    //if(isset($_GET["newlength"])){
                    $newlength = getorpost("newlength");
                    //}

                    $this->updateDbField($table, $field, $newtype, $newlength, $oldtype, $default);

                    break;


                case "delete":

                    if (isset($_GET["table"]) && isset($_GET["field"])) {

                        $table = getorpost("table");
                        $field = getorpost("field");

                        $this->deleteDbField($table, $field);


                    }

                    break;

                case "createField":


                    $table = getorpost("table");
                    $field = getorpost("field");
                    $newtype = getorpost("newtype");
                    //$newlength = NULL;
                    //if(isset($_GET["newlength"])){
                    $newlength = getorpost("newlength");
                    //}
                    $default = getorpost("default");

                    $this->createDbField($table, $field, $newtype, $newlength, $default);

                    break;

                case "createTable":

                    $table = getorpost("table");

                    $this->createTable($table);
                    break;

                case "createLinktablefield":
                    $db = bpfw_getDb();
                    $table = $db->escape_string(getorpost("table"));
                    $field = $db->escape_string(getorpost("field"));

                    $linktable_field = $db->escape_string(getorpost('linktable_field'));

                    $linktablename = bpfw_createModelByName($table)->getLinkTableName($field);

                    $query = "ALTER TABLE `$linktablename` ADD COLUMN `$linktable_field` int(11) DEFAULT NULL;";
                    $resp = $db->makeQuery($query);
                    var_dump($resp);
                    echo "ausgeführt:" . $query;

                    //


                    /*
                    CREATE TABLE `link_product_cross_sell_ids` (
                      `cross_sell_idsIds` int(11) NOT NULL,
                      `productId` int(11) DEFAULT NULL,
                      `productId2` int(11) DEFAULT NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; */


                    // $this->createDbField($table,$field,$newtype,$newlength, $default);


                    break;

                case "renameLinktablefield":

                    /*    $table=$db->escape_string($table);
                        $newname=$db->escape_string($newname);
                        $oldname=$db->escape_string($oldname);*/
                    $db = bpfw_getDb();

                    $table = getorpost("table");
                    $field = getorpost("field");

                    $linktable_field = $db->escape_string(getorpost('linktable_field'));
                    $linktable_field_new = $db->escape_string(getorpost('linktable_oldname'));

                    $table = $db->escape_string($table);
                    $field = $db->escape_string($field);

                    $linktablename = bpfw_createModelByName($table)->getLinkTableName($field);

                    $query = "ALTER TABLE $linktablename CHANGE `$linktable_field` `$linktable_field_new` int(11) DEFAULT NULL;";
                    $db->makeQuery($query);

                    echo $query;


                    break;


            }

        }
        // }


        parent::handleActions();

    }

    /**
     * @throws Exception
     */
    function renameField($table, $newname, $oldname, $sqltype, $primarykey = true, $default = "")
    {

        $db = bpfw_getDb();

        $table = $db->escape_string($table);
        $newname = $db->escape_string($newname);
        $oldname = $db->escape_string($oldname);


        $typeForNew = $sqltype;

        $defaultsql = "NULL";
        if (!empty($default)) {
            // $defaultsql = "DEFAULT '$default'";


            if ($default == "NOW()") {
                $defaultsql .= " DEFAULT NOW()";
            } else if ($default == "NULL") {
                $defaultsql .= " DEFAULT NULL";
            } else {
                $defaultsql .= " DEFAULT '$default'";
            }

        }


        // ALTER TABLE `order` CHANGE `orderId` `orderId3` INT(11) NOT NULL AUTO_INCREMENT;

        if ($primarykey) {
            $typeForNew .= " NOT NULL AUTO_INCREMENT ";
            $defaultsql = "";
        }

        $sql = "ALTER TABLE `$table` CHANGE `$oldname` `$newname` $typeForNew $defaultsql";
        $error = bpfw_getDb()->makeQuery($sql);

        if (empty($error) || $error == 1) {
            $this->commandsExecuted[] = "rename Erfolgreich ausgeführt: $sql";
        } else {
            $this->commandsExecuted[] = "rename Fehler aufgetreten: $sql - " . json_encode($error);
        }

    }

    /**
     * @throws Exception
     */
    function updateDbField($table, $field, $newtype, $newlength, $oldtype, $default = "")
    {

        if ($newtype == BpfwDbFieldType::TYPE_LINK_TABLE) {


            bpfw_createModelByName($table)->createLinkTableInDatabase($field);

            // altes Feld löschen
            $this->deleteDbField($table, $field);


        } else {

            if ($newtype == BpfwDbFieldType::TYPE_IGNORE) {
                $this->deleteDbField($table, $field);
                return null;
            }
            if ($newtype == BpfwDbFieldType::TYPE_FOREIGN) {
                $this->deleteDbField($table, $field);
                return null;
            }
            if ($oldtype == BpfwDbFieldType::TYPE_LINK_TABLE) {
                $this->createDbField($table, $field, $newtype, $newlength, $default);
                return null;
            }

            $db = bpfw_getDb();

            $typeForNew = $newtype;
            if (!empty($_GET["newlength"]) && $_GET["newlength"] != "default") {
                $newlength = $_GET["newlength"];
                $typeForNew = "$newtype($newlength)";
            }

            $defaultsql = "NULL";
            if (!empty($default) || $default === 0 || $default === "0") {
                if ($default == "NOW()") {
                    $defaultsql .= " DEFAULT NOW()";
                } else if ($default == "NULL") {
                    $defaultsql .= " DEFAULT NULL";
                } else {
                    $defaultsql .= " DEFAULT '$default'";
                }
            }

            $sql = " ALTER TABLE `$table` MODIFY `$field` $typeForNew $defaultsql;";

            $error = $db->makeQuery($sql);

            if (empty($error) && $error != -1 || $error == 1) {
                $this->commandsExecuted[] = "modify Erfolgreich ausgeführt: $sql";
            } else {
                $this->commandsExecuted[] = "modify Fehler aufgetreten: $sql - " . json_encode($error);
            }

        }

    }

    /**
     * @throws Exception
     */
    function deleteDbField($table, $field)
    {


        // if(!empty($field->type) && $field->type->type == BpfwDbFieldType::TYPE_IGNORE)return;

        $db = bpfw_getDb();


        $sql = " ALTER TABLE `$table` DROP `$field`;";

        $error = $db->makeQuery($sql);

        if (empty($error) || $error == 1) {
            $this->commandsExecuted[] = "delete Erfolgreich ausgeführt: $sql";
        } else {
            $this->commandsExecuted[] = "delete Fehler aufgetreten: $sql - " . json_encode($error);
        }

    }

    /**
     * @throws Exception
     */
    function createDbField($table, $field, $newtype, $newlength, $default = "")
    {


        if ($newtype == BpfwDbFieldType::TYPE_LINK_TABLE) {

            bpfw_createModelByName($table)->createLinkTableInDatabase($field);

        } else {

            if (isset($field) && isset($field->type) && $field->type->type == BpfwDbFieldType::TYPE_IGNORE) return;
            if (isset($field) && isset($field->type) && $field->type->type == BpfwDbFieldType::TYPE_FOREIGN) return;

            $db = bpfw_getDb();

            $typeForNew = $newtype;
            if (!empty($_GET["newlength"]) && $_GET["newlength"] != "default") {
                $newlength = $_GET["newlength"];
                $typeForNew = "$newtype($newlength)";
            }

            $defaultsql = "NULL";
            if (!empty($default)) {
                if ($default == "NOW()") {
                    $defaultsql .= " DEFAULT NOW()";
                } else if ($default == "NULL") {
                    $defaultsql .= " DEFAULT NULL";
                } else {
                    $defaultsql .= " DEFAULT '" . $db->escape_string($default) . "'";
                }
            }

            $sql = " ALTER TABLE `$table` ADD `$field` $typeForNew $defaultsql;";

            $error = $db->makeQuery($sql);

            if (empty($error) || $error == 1) {
                $this->commandsExecuted[] = "add Erfolgreich ausgeführt: $sql";
            } else {
                $this->commandsExecuted[] = "add Fehler aufgetreten: $sql - " . json_encode($error);
            }

        }

    }

    /**
     * @throws Exception
     */
    function createTable($table)
    {

        //  $db = bpfw_getDb();

        $model
            = bpfw_createModelByName($table);


        $error = $model->createTable();

        if (empty($error) || $error == 1) {
            $this->commandsExecuted[] = "create Erfolgreich ausgeführt";
        } else {
            $this->commandsExecuted[] = "create Fehler aufgetreten: - " . json_encode($error);
        }

    }

}