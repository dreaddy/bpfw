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



const POSITION_LEFT = "left";
const POSITION_RIGHT = "right";
const POSITION_FULLWIDTH = "fullwidth";


/**
 * @param $data_header BpfwModelFormfield[] fieldheader you can get with (model)->getDbModel()
 * @return int editable fields in model
 * @throws Exception
 */
function bpfw_countEditableFields(array $data_header): int
{
    $count = 0;

    if (empty($data_header)) {
        throw new Exception("dataheader is null on bpfw_countEditableFields");
    }

    foreach ($data_header as $hkey => $hvalue) {
        if ($hvalue->type != 'hidden') {
            $count++;
        }
    }

    return $count;
}

/**
 * @return string pageslug
 */
function bpfw_getActivePage() : string
{

    if (!bpfw_isLoggedIn()) {
        // return "login";
    }

    if (empty(getorpost('p'))) {
        $_GET['p'] = STARTING_PAGE;
    }

    //  var page = jQuery("body").data("page"); would also work

    return getorpost('p');

}

/**
 * is the current page that is loading an ajax call
 *
 * @return bool
 */
function bpfw_isAjax(): bool
{
    return getorpost("command") != null;
}



$creatingTables = false;
function bpfw_creatingTables(): bool
{
    global $creatingTables;

    return $creatingTables || isset($_GET["createadmin"]);
}

function bpfw_setCreatingTables($s_creatingTables): void
{
    global $creatingTables;
    $creatingTables = $s_creatingTables;
}



/**
 * Summary of bpfw_compare_sort
 * @param DatabaseSortEntry[] $sort1
 * @param DatabaseSortEntry[] $sort2
 * @return boolean is identical
 */
function bpfw_compare_sort(array $sort1, array $sort2): bool
{
    if ($sort1 == $sort2) return true;

    if (!empty($sort1) && empty($sort2)) {
        return false;
    }

    if (!empty($sort2) && empty($sort1)) {
        return false;
    }

    if (count($sort1) != count($sort2)) {
        return false;
    }

    foreach ($sort1 as $key => $sort1value) {

        if (!isset($sort2[$key]) || $sort2[$key]->direction != $sort1value->direction || $sort2[$key]->fieldName != $sort1value->fieldName) {
            return false;
        }

    }

    return true;

}


// echo date("d.m.Y", strtotime("2020-08-01"));

/**
 * @param $type string user or customer
 * @param $id int user or customerid
 * @return string the url the user has to click
 */
function bpfw_get_unsubscribe_link(string $type, int $id): string
{
    $code = bpfw_loadSetting(SETTING_UNSUBSCRIBE_CALL_CODE);
    $hstr = ($id * 31) . $type . $code;
    return BASE_URI . "?unsubscribe=$id&t=$type&h=" . md5($hstr) . "&logout";

}


function bpfw_verify_unsubscribe_link($id, $type, $hash): bool
{

    $code = bpfw_loadSetting(SETTING_UNSUBSCRIBE_CALL_CODE);

    $hstr = ($id * 31) . $type . $code;
    $hash_should = md5($hstr);

    if ($hash_should == $hash) return true;

    return false;

}


/**
 * @param $type string user or customer
 * @param $id int user or customerid
 * @param $hash string secret hash
 * @return bool
 * @throws Exception
 */
function bpfw_unsubscribe_newsletter($id, $type, $hash): bool
{

    if (bpfw_verify_unsubscribe_link($id, $type, $hash)) {

        $model = bpfw_createModelByName($type);
        $db = bpfw_getDb();
        $valid = $db->makeUpdate(array("newsletter_mail" => new DbSubmitValue("newsletter_mail", 0, $model)), $type, new DatabaseKey($model->tryGetKeyName(), $id, 'i'), true);

        if ($valid == 1) {
            return true;
        }

    }

    return false;

}


function bpfw_isDeleteAction(): bool
{
    return isset($_GET['delete']) && is_numeric($_GET['delete']);
}

function bpfw_isAddAction(): bool
{
    return isset($_GET['addAction']) && is_numeric($_GET['addAction']) || isset($_GET['openadd']) || isset($_GET['openAdd']);
}

function bpfw_isEditOrDuplicateAction(): bool
{
    return bpfw_isEditAction() || bpfw_isDuplicateAction();
}


function bpfw_isEditAction(): bool
{
    return isset($_GET['editAction']) && is_numeric($_GET['editAction']) || isset($_GET['edit']) && is_numeric($_GET['edit']);
}

function bpfw_isDuplicateAction(): bool
{
    return isset($_GET['editAction']) && is_numeric($_GET['editAction']) || isset($_GET['duplicate']) && is_numeric($_GET['duplicate']);
}


/**
 * set values in variable array with assigned prefix. used a lot for pdf generation
 * @param DbModelEntry|null $dbModelEntry kvp array value
 * @param string $prefix variable is saved as prefix.value example: user.name
 * @param array $variables array with previous set variables
 * @return array
 * @throws Exception
 */
function bpfw_setVariables(?DbModelEntry $dbModelEntry, string $prefix, array $variables): array
{


    if (empty($dbModelEntry)) return $variables;

    /*if(empty($prefix)){
    throw new Exception("no dbmodelentry set");
    }

    if(empty($dbmodelentry)){
    $emptymodel = bpfw_createModelByName($prefix);
    foreach($emptymodel->getDbModel() as $key=>$values){
    $variables[$prefix.".".$key] = "not set";
    }
    return $variables;
    }*/

    $values = $dbModelEntry->GetKeyValueArray(false);
    if (empty($values)) {
        return $variables;
    }
    $rowkey = $dbModelEntry->{$dbModelEntry->model->tryGetKeyName()};
    foreach ($values as $key => $value) {

        $field = $dbModelEntry->model->getDbModel()[$key];
        //$hvalue = $dbmodelentry->model->getDbModel()[];
        $componentinfo = bpfw_getComponentHandler()->getComponent($field->display);

        /** @noinspection PhpParamsInspection */
        $val = $componentinfo->GetDisplayFormattedPlainValue($value, $key, $field, $rowkey, $dbModelEntry->model);

        $variables[$prefix . "." . $key . "_display"] = $val;

        /*
        if ($field->entries != null) {
            $variables[$prefix . "." . $key . "_display"] = $field->entries->getValueByKey($value);
        } else {
            $variables[$prefix . "." . $key . "_display"] = $val;
        }*/

        $variables[$prefix . "." . $key] = $value;
    }


    return $variables;

}


/**
 * generate sql friendly string from dbmodel
 * @param BpfwModel $model2 model where data is taken from
 * @param mixed $listfieldname fieldname (or calculated field) to display
 * @param mixed $pk foreign key name to use
 * @param mixed $rowkey foreign key value to use
 * @return string
 * @throws Exception
 */
function bpfw_generateKeyvaluestringFromDbfield(BpfwModel $model2, mixed $listfieldname, mixed $pk, mixed $rowkey): string
{

    $retval = "";

    $values = $model2->DbSelectKeyValueArray($listfieldname, " where " . $pk . " = '" . $rowkey . "'");


    // echo $pk." = '".$rowkey."'";

    $first = true;
    foreach ($values as $key => $value) {
        if (!$first) $retval .= ", ";


        if (isset($fieldDbModel->datamodel_list_displayhandler)) {
            $retval .= $fieldDbModel->datamodel_list_displayhandler->getValueByKey($value);
        } else {
            $retval .= $value;
        }


        $first = false;

    }


    return $retval;


}





const FILTER_FORMFIELD_VALIDATION = "FILTER_FORMFIELD_VALIDATION";








/**
 * Sets Metadata for type/id pair. For Exampe "User" 23.
 * @param mixed $type string as type ("user", "order" ... )
 * @param mixed $id id - typically the PK of the table
 * @param array|string $json_data json or array of additional metadata
 * @param mixed|bool $updateExisting update/overwrite data if existing in database
 * @return int metadataId of new or existing entry
 * @throws Exception
 */
function bpfw_setMetadata(mixed $type, mixed $id, array|string $json_data, bool $updateExisting = true): int
{
    return bpfw_createModelByName("metadata")->setMetadata($type, $id, $json_data, $updateExisting);
}



/**
 * Get Metadata by modelname/id
 * @param mixed $type string as type ("user", "order" ...
 * @param mixed $id id - typically the PK of the table
 * @return null|array if existing returns array with values (metadataId, type, id, json_data) NULL otherwise
 * @throws Exception
 */
function bpfw_getMetadata(mixed $type, mixed $id): ?array
{

    return bpfw_createModelByName("metadata")->getMetadata($type, $id);

}

/**
 * return slug of active app
 * @return string
 */
function bpfw_getActiveApp() : string
{

    if (!empty($_SESSION["ACTIVE_APP"]))
        return $_SESSION["ACTIVE_APP"];

    return "";

}






/**
 * add Spoiler hint to text
 * @param array|string $label
 * @param string $spoilertext
 * @return array|string
 */
function bpfw_addSpoilerHint(array|string $label, string $spoilertext): array|string
{

    if (empty($spoilertext)) return $label;

    if (is_array($label)) {
        return $label;
    }

    $spoilericon = "<div class='spoilericon'>?<div class='spoilercontainer'>" . $spoilertext . "</div></div>";


    return $label . " " . $spoilericon;


}



