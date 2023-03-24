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

/** @noinspection PhpPossiblePolymorphicInvocationInspection */


/**
 * dbmodel with the capability to manipulate the Database, rights management and some other stuff for the bpfw mvc System
 */
abstract class BpfwModel extends DbModel
{

    const FILTER_ENTRY_SELECT_WHERE = "FILTER_ENTRY_SELECT_WHERE";
    const FILTER_ENTRY_SELECT_JOIN = "FILTER_ENTRY_SELECT_JOIN";


    //const FILTER_EDITDIALOG_BEFORE_HEADER_CLOSE_ICON = "FILTER_EDITDIALOG_BEFORE_HEADER_CLOSE_ICON";

    // const CHECK_PERMISSION = "CHECK_PERMISSION";
    var bool $search_enabled = true;
    var DefaultControl $control;
    var DefaultView $view;
    var string $filteredField = "";
    var string $filteredModel = "";
    var int $minimumMaxTabs = 0;
    var int $minimumMaxTabsAdd = -1;
    var int $minimumMaxTabsEdit = -1;
    var array $tabsWithoutTabFunction = array();
    var bool $isRootHierachy = true;

    /**
     * name of a view Classname
     * only set if no specific view is existing for this model and you dont want to use the default
     * @var string
     */
    var string $viewtemplate = "defaultlistmodalView";

    /**
     * name of a control Classname
     * only set if no specific view is existing for this model and you dont want to use the default
     * @var string
     */
    var string $controltemplate = "defaultControl";


    /**
     * Summary of $submitValues
     * @var DbSubmitValue[]
     */
    var array $submitValues = array();
    public int $pagesAdd = 1;
    public int $pagesEdit = 1; // eventId etc...
    public string $error = ""; // event etc...
    public string $exception = ""; // array of tab nums that are always created
    public bool $showdata = true; // array of tab nums that are always created
    public bool $showNavigation = true; // array of tab nums that are always created
    public bool $fullsizeHeaderbar = DEFAULT_FULLSIZE_HEADERBAR;
    public bool $showHeader = true;
    public bool $showHeader2 = true;
    public bool $showFooter = true; // defaultlistView
    public string $sortOrder = "desc";
    public int $sortColumn = 1;


    /** @noinspection PhpUnusedParameterInspection */
    public string $minUserrankForShow = USERTYPE_ADMIN;
    public string $minUserrankForEdit = USERTYPE_ADMIN;
    public string $minUserrankForDuplicate = USERTYPE_ADMIN;
    public string $minUserrankForDelete = USERTYPE_ADMIN;
    public string $minUserrankForAdd = USERTYPE_ADMIN;
    public string $minUserrankForPrint = USERTYPE_CONSULTANT;
    public bool $ignoreOnSync = false;
public bool $ignoreOnImportExport = false;
    public bool $showTabs = true;


    /** @noinspection PhpUnused */
    public bool $showPrevNextButtonsOnAddDialog = false;
    public bool $showPrevNextButtonsOnEditDialog = false;
    public bool $showCancelButton = true;
    public bool $showSubmitButton = true;
    public bool $showTable = true;
    public bool $showTopButtons = true;
    protected string $subtitle = "??";

    /**
     * Summary of __construct
     * @throws Exception
     */
    public function __construct()
    {

        parent::__construct();

        $this->submitValues = array();

        foreach ($this->getDbModel() as $key => $value) {

            $this->submitValues[$key] = new DbSubmitValue($key, getorpost($key), $this);

        }

        $foldergroup = null;
        if (!empty(getorpost("filter"))) {
            $foldergroup = getorpost("filter");
        } else if (!empty($this->tryGetKeyName()) && isset($this->submitValues[$this->getKeyName()])) {
            $foldergroup = $this->submitValues[$this->getKeyName()]->data;
        }

        // add $_FILE to model
        foreach ($this->submitValues as $key => $value) {

            if (is_array($value->getDbField())) {
                echo "<p style='color:red'>Fehler bei Submitvalue <b>$key</b> -> Model init erwartet bpfwmodelformfield, kvp array erhalten</p>";


            } else {


                if ($value->getDbField()->display == "file" || $value->getDbField()->display == "image") { // has_blobdata


                    if (isset($_FILES[$key])) {
                        $this->submitValues[$key]->filedata = $_FILES[$key];
                    }

                    if (!empty($this->submitValues[$key]->filedata) && !empty($this->submitValues[$key]->filedata["tmp_name"])) {
                        $filedata = $this->submitValues[$key]->filedata;
                        $newdata = bpfw_handleFileUpload($this->GetTableName(), $foldergroup, $filedata);
                        $this->submitValues[$key]->data = $newdata;
                    } else {
                        unset($this->submitValues[$key]);
                    }

                }

            }

        }


        bpfw_add_filter(BpfwModel::FILTER_ENTRY_SELECT_WHERE, $this->GetTableName(), array($this, "addFilterToWhere"), 10, 5);

        // bpfw_add_filter(BpfwModel::FILTER_ENTRY_SELECT_JOIN, array($this, "addFilterToJoin"), 10, 5);

    }

    /**
     * @throws Exception
     */
    function selectByExternalId($external_id, $shopId): array
    {
        // todo: move to wws ...
        if (isset($this->getDbModel()["external_id"]) && isset($this->getDbModel()["shopId"])) {
            return $this->DbSelectSingleOrNullByWhere(" external_id = $external_id and shopId = $shopId");
        }

        throw new Exception("Custom Function selectByExteralId is not implemented in model and the fields external_id / shopId are not existing. Please implement custom function in model " . $this->GetTableName() . ".");

    }

    /**
     * @throws Exception
     */
    function getExternalId($key, $values = null): int
    {

        if (empty($values)) {
            $values = $this->GetEntry($key);
        }

        return ($values->external_id ?? -1);

    }

    /**
     * Summary of GetEntry
     * @param int|string|null $key
     * @param string $where
     * @param int $count
     * @param int $offset
     * @param string $join
     * @param bool $temptable
     * @param bool $disablePermissionCheck
     * @return DbModelEntry|null
     * @throws Exception
     */
    public function GetEntry(int|string|null $key, string $where = "default", int $count = -1, int $offset = 0, string $join = "", bool $temptable = false, bool $disablePermissionCheck = false): ?DbModelEntry
    {

        if($key == null)return null;
        
        if (!$disablePermissionCheck && !bpfw_hasPermission($this->minUserrankForShow)) {
            throw new Exception("GetEntry show not allowed ( " . bpfw_getUsertype() . " / $this->minUserrankForShow ) , not logged in?");
        }

        $tablename = $this->GetTableName();

        if ($temptable) {
            $tablename = $this->GetTempTableName();
        }

        if ($where == "default") {
            $where = " " . $tablename . "." . $this->tryGetKeyName() . " = '$key' ";
        }

        $join = bpfw_do_filter(BpfwModel::FILTER_ENTRY_SELECT_JOIN, $this->GetTableName(), array($join, $where, $count, $offset, null), $this);

        $where = bpfw_do_filter(BpfwModel::FILTER_ENTRY_SELECT_WHERE, $this->GetTableName(), array($where, $count, $offset, null, $join), $this);


        $data = parent::DbCachedSelectAndCreateObjectArray($where, $count, $offset, array(), $join, $temptable);

        if ($key < 0) {
            throw new Exception("invalid key " . $key);
        } else {

            return $data[$key] ?? null;

        }

    }

    // default filter

    /** @noinspection PhpUnusedParameterInspection */

    /**
     * @throws Exception
     * @noinspection PhpUnused
     */
    function updateExternalId($key, $shopId, $external_id)
    {

        if (isset($this->getDbModel()["external_id"]) && isset($this->getDbModel()["shopId"])) {

            $db = bpfw_getDb();

            $query = " update " . $this->GetTableName() . " set external_id = '" . (int)$external_id . "' where shopId = " . (int)$shopId . " and " . $this->tryGetKeyName() . " = '" . (int)$key . "'";
            //echo "query is $query";
            $db->makeQuery($query);

            //$this->DbUpdate(array($this->tryGetKeyName()=>$key, "external_id"=>$external_id))

        }

    }

    /**
     * @throws Exception
     * @noinspection PhpUnused
     */
    function updateRemoteModified($key, $shopId)
    {

        if (isset($this->getDbModel()["remote_modified"]) && isset($this->getDbModel()["shopId"])) {

            $db = bpfw_getDb();
            $query = " update " . $this->GetTableName() . " set remote_modified = NOW() where shopId = " . (int)$shopId . " and " . $this->tryGetKeyName() . " = '" . (int)$key . "'";
            // echo "query is $query";
            $db->makeQuery($query);

        }

    } // wie viele Seiten gibt es im add mode. TODO: automatisch ermitteln und geh�rt eher in View

    /**
     * Summary of getFieldnamesForListFromDbModelOnly
     * @return array
     * @throws Exception
     */
    function getFieldnamesForListFromDbModelOnly(): array
    {

        $dbmodel = $this->getDbModel();

        $retval = array();

        foreach ($dbmodel as $fieldname => $hvalue) {


            $componentinfo = bpfw_getComponentHandler()->getComponent($hvalue->display);
            if (isset($this->view->print) && $this->view->print && $hvalue->hiddenOnPrint) continue;
            if ($hvalue->hiddenOnList) continue;
            if (!empty($hvalue->parentComponent)) continue;
            if (!$componentinfo->showOnList) {
                continue;
            }


            $retval[$fieldname] = $fieldname;
        }


        return $retval;

    } // wie viele Seiten gibt es im edit mode. TODO: automatisch ermitteln und geh�rt eher in View

    /**
     * Get Fields that should be shown on the html datatable list
     * @return BpfwModelFormfield[]
     * @throws Exception
     */
    function getFieldsForList(): array
    {

        $dbmodel = $this->getDbModel();

        $fieldsToReturn = $this->getListFieldsArray();

        $retval = array();

        if (is_array($fieldsToReturn)) {


            foreach ($fieldsToReturn as $fieldname) {

                if (!isset($dbmodel[$fieldname])) {
                    throw new Exception("$fieldname not existing");
                }


                $hvalue = $dbmodel[$fieldname];

                $componentinfo = bpfw_getComponentHandler()->getComponent($hvalue->display);
                if ($this->view->print && $hvalue->hiddenOnPrint) continue;
                // if($hvalue->hiddenOnList)continue;
                if (!empty($hvalue->parentComponent)) continue;
                if (!$componentinfo->showOnList) {
                    continue;
                }


                $retval[$fieldname] = $hvalue;
            }


        } else if ($fieldsToReturn == "read_from_dbmodel") {

            $retval = $this->getFieldsForListFromDbModelOnly();

        }

        return $retval;

    }

    /**
     * array of fieldnames to show in datagrid list.
     * default is reading it from dbmodel and ignoring LISTSETTING::HIDDENONLIST=>true
     * @return string
     * @noinspection PhpMissingReturnTypeInspection
     */
    function getListFieldsArray()
    {

        return "read_from_dbmodel";

    }

    /**
     * Summary of getFieldsForListFromDbModelOnly
     * @return BpfwModelFormfield[]
     * @throws Exception
     */
    function getFieldsForListFromDbModelOnly(): array
    {

        $dbmodel = $this->getDbModel();


        $retval = array();

        foreach ($dbmodel as $fieldname => $hvalue) {
            $componentinfo = bpfw_getComponentHandler()->getComponent($hvalue->display);

            if (isset($this->view->print) && $this->view->print && $hvalue->hiddenOnPrint) continue;
            if ($hvalue->hiddenOnList) continue;
            if (!empty($hvalue->parentComponent)) continue;
            if (!$componentinfo->showOnList) {
                continue;
            }


            $retval[$fieldname] = $hvalue;
        }


        return $retval;

    }

    /**
     * @throws Exception
     */
    function createDialogId(): string
    {

        return "dialog_" . time() . "_" . random_int(0, PHP_INT_MAX);

    }

    /**
     * @throws Exception
     */
    function getPositions(): array
    {
        $positions = array();
        foreach ($this->getDbModel() as $hvalue) {
            $positions[$hvalue->position] = $hvalue->position;
        }
        return $positions;
    }

    public function setView($view)
    {
        $this->view = $view;
    }

    /**
     * @throws Exception
     */
    function addFilterToWhere($where, $count, $offset, $sort, $join)
    {

        if (!empty($this->filteredField)) {

            if (empty($where)) $where = " 1 ";
            $filterVal = "";

            $filter = getorpost("filter");
            if (!empty(getorpost("filter"))) // || $filter === "0" || $filter === 0)
                $filterVal = trim(getorpost("filter"));

            if ($filterVal !== 'false' && $filterVal !== '') {

                if (!is_numeric($filterVal)) {
                    throw new Exception("filter is no number: '$filterVal' " . $this->filteredField);
                }
                $where = "( $where ) AND " . $this->filteredField . " = " . $filterVal;

            }
        }

        return $where;

    }

    /** @noinspection PhpUnusedParameterInspection
     * @noinspection PhpUnused
     */
    function addFilterToJoin($join, $where, $count, $offset, $sort)
    {
        if (!empty($this->filteredField)) {

            echo $join;
        }

        return $join;

    }

    public function getSlug(): string
    {
        return $this->GetTableName();
    }



    public abstract function GetTitle(): string; // TODO: kann eher in view

    /**
     * @throws Exception
     */
    public function getUsedFieldTypes()
    {

        $usedFieldTypes = $this->addTypeOfDbModelToArray($this->getDbModel(), array("types" => array(), "models" => array()));

        return $usedFieldTypes["types"];

    }

    /**
     * Summary of addTypeOfDbModelToArray
     * @param BpfwModelFormfield[] $dbmodel
     * @param mixed $usedFieldTypes
     * @return mixed
     * @throws Exception
     */
    public function addTypeOfDbModelToArray(array $dbmodel, mixed $usedFieldTypes): mixed
    {

        foreach ($dbmodel as $field) {

            if (empty($usedFieldTypes["types"][$field->display])) {
                $usedFieldTypes["types"][$field->display] = $field->display;
            }

            if (isset($field->datamodel)) {

                if (!isset($usedFieldTypes["models"][$field->datamodel])) // endless recursion prevention
                {


                    $filter = $field->datamodel_filter;

                    $usedFieldTypes["models"][$field->datamodel] = $field->datamodel;

                    if ($filter == "formid") $filter = getorpost("rowid");

                    $model = ModelLoader::findAndCreateModel($field->datamodel, $filter);

                    $usedFieldTypes = $this->addTypeOfDbModelToArray($model->getDbModel(), $usedFieldTypes);

                    /*
                    $current = $field->datamodel;

                    if(file_exists(APP_MVC_PATH."models/".$current."Model.inc.php")){
                        require_once(APP_MVC_PATH."models/".$current."Model.inc.php");
                    }else if(file_exists(BPFW_MVC_PATH."models/".$current."Model.inc.php")){
                        require_once(BPFW_MVC_PATH."models/".$current."Model.inc.php");
                    }else{
                        echo APP_MVC_PATH."models/".$current."Model.inc.php";
                        echo BPFW_MVC_PATH."models/".$current."Model.inc.php";
                        throw new Exception("model not existing for ".$current);
                    }

                      $classnames = array(ucwords($current)."Model", strtolower($current)."Model",  strtolower($current."Model"), $current."Model");

        //var_dump($classnames);

                      foreach($classnames as $val)
                      {

                          if(class_exists($val))
                          {

                             // static::$currentClassName = $val;

                              $model = new $val();

                              $usedFieldTypes = $this->addTypeOfDbModelToArray($model->getDbModel(), $usedFieldTypes);

                          }

                      }*/

                }

            }

        }

        return $usedFieldTypes;

    }

    /**
     * Summary of GetData
     * @param string $where
     * @param int $count
     * @param int $offset
     * @param DatabaseSortEntry[] $sort
     * @param string $join
     * @param bool $temptable
     * @return DbModelEntry[]
     * @throws Exception
     */
    public function GetEntries(string $where = " 1", int $count = -1, int $offset = 0, array $sort = array(), string $join = "", bool $temptable = false): array
    {

        $join = bpfw_do_filter(BpfwModel::FILTER_ENTRY_SELECT_JOIN, $this->GetTableName(), array($join, $where, $count, $offset, $sort), $this); // must be real tablename, not temptable

        $where = bpfw_do_filter(BpfwModel::FILTER_ENTRY_SELECT_WHERE, $this->GetTableName(), array($where, $count, $offset, $sort, $join), $this); // must be real tablename, not temptable

        if (!bpfw_hasPermission($this->minUserrankForShow)) {
            return array();
        }

        if (empty($sort) && !is_array($sort)) $sort = array();

        return parent::DbCachedSelectAndCreateObjectArray($where, $count, $offset, $sort, $join, $temptable);

    }

    /**
     * @throws Exception
     */
    public function CountAllEntries($where = '', $join = '', $temptable = false)
    {

        if (!$this->showdata) return -1;

        $db = bpfw_getDb();

        $tablename = $this->GetTableName();
        if ($temptable) $tablename = $this->GetTempTableName();

        $join = bpfw_do_filter(BpfwModel::FILTER_ENTRY_SELECT_JOIN, $this->GetTableName(), array($join, $where, -1, -1, null), $this);

        $where = bpfw_do_filter(BpfwModel::FILTER_ENTRY_SELECT_WHERE, $this->GetTableName(), array($where, -1, -1, null, $join), $this);

        $entriesFound = -1;
        if ($this->tryGetKeyName() != null) { // kein KEy im aktuellen Model. Trotzdem z�hlen?
            try{
                $entriesFound = $db->countTableEntries($tablename, $this->getKeyName(), $where, $join);
            }catch(Exception $ex){
                echo "error in CountAllEntries: " . $ex->getMessage();
            }
        }

        return $entriesFound;

    }

    function getKvpArray($values): array
    {
        $kvpValues = array();


        foreach ($values as $k => $v) {

            if (is_object($v) && get_class($v) == "DbSubmitValue") {
                $kvpValues[$k] = $v->data;
            } else {
                $kvpValues[$k] = $v;
            }

        }

        return $kvpValues;
    }

    /**
     * Summary of EditEntry
     * @param DbSubmitValue[]|array $data // |DbModelEntry keyvalue or data array or DbModelEntry
     * @throws Exception
     */
    public function EditEntry(array $data, bool $ignoreRightsManagement = false, bool $ignoreConversion = false, bool $temptable = false): void
    {

        $dataNormalized = $this->CreateDbSubmitvalueArrayFromKeyValueArray($data);

        $dbmodel = $this->getDbModel();
        $d2 = array();
        $kvp = array();

        foreach ($dataNormalized as $k => $v) {

            // echo $k; echo json_encode($v); echo "<br>"; ob_flush();

            $val = $v; //->data;

            $errorArray = bpfw_getComponentHandler()->getComponent($dbmodel[$k]->display)->validateValue(array(), "edit", $val, $dbmodel[$k], $dataNormalized, $k, $this);

            if (!empty($errorArray)) {
                foreach ($errorArray as $error) {
                    bpfw_error_add($error, array("type" => "formvalidation", "key" => $k, "value" => $val, "page" => $dbmodel[$k]->editpage));
                }
            }

            if ($dbmodel[$k]->hiddenOnEdit) {
                continue;
            }

            if ($dbmodel[$k]->doNotEdit) {
                continue;
            }

            // if(!isset($v->data))continue;

            // if(!isset($v->key))continue;

            // TODO: data is null

            if ($dbmodel[$k]->display == "signature") {
                if (empty($v->data)) {
                    $olderval = getorpost($k . "_duplicate");
                    if (isset($olderval)) {
                        $v->data = getorpost($k . "_duplicate");
                    }
                }
            }

            $d2[$k] = $val;


        }

        $dataNormalized = $d2;

        if (!$ignoreRightsManagement) {
            if (!bpfw_hasPermission($this->minUserrankForEdit)) {
                throw new Exception("edit not allowed");
            }
        }

        if (!bpfw_error_hasErrors()) {

            if (!$temptable) {

                $metadata = $d2;
                unset($metadata["password"]);
                $metadata = json_encode($metadata);

                $id = $dataNormalized[$this->tryGetKeyName()];
                if (!empty($id) && is_object($id) && get_class($id) == "DbSubmitValue") {
                    $id = $id->data;
                }

                bpfw_logDataEntry($this->GetTableName(), $id, "update", $metadata);

            }

            $rowsAffected = $this->DbUpdate($dataNormalized, null, $ignoreConversion, $temptable);
            if (!bpfw_error_hasErrors()) {

                if ($rowsAffected < 0) {
                    $this->exception = "edit failed";
                } else if ($rowsAffected > 1) {
                    $this->exception = "DBUpdate -> >1 row affected !!! " . json_encode($dataNormalized);
                }

                if (!empty($this->exception)) {
                    throw new Exception($this->exception);
                }

            }
        }
    }

    /**
     * @throws Exception
     */
    function removeLogEntries($key)
    {
        return bpfw_createModelByName("logdataactions")->removeLogEntryByKey($this->GetTableName(), $key);
    }

    /**
     * Summary of DeleteEntry
     * @param int $key primary key
     * @throws Exception
     */
    public function DeleteEntry(int $key, bool $temptable = false): bool
    {

        if (!bpfw_hasPermission($this->minUserrankForDelete)) {
            throw new Exception("delete not allowed");
        }


        if (!bpfw_error_hasErrors()) {

            if (!$temptable) {
                $shopId = 1;

                $logDataAction = bpfw_createModelByName("logdataactions");

                $fullEntry = $this->DbSelectSingleOrNullByKey($key);

                if (empty($fullEntry)) {
                    // Eintrag gibt es nicht, also auch nicht loggen
                    return false;
                } else {

                    $parentkeyname = $this->tryGetParentKeyName();
                    $parentkey = null;
                    if (!empty($parentkeyname)) {
                        $parentkey = $fullEntry[$parentkeyname];
                    }
                    $logDataAction->logNewEntry($this->GetTableName(), $key, $parentkey, $shopId);
                    $metadata = "{{{readEntries}}}";
                    bpfw_logDataEntry($this->GetTableName(), $key, "delete", $metadata);
                }
            }

            $rowsAffected = $this->DbDeleteByPrimaryKey($key, $temptable);

            if ($rowsAffected < 0) {
                $this->exception = "delete failed";

                if (!$temptable) {
                    // bpfw_createModelByName("logdataactions")->removeLogEntryByKey($this->GetTableName(), $key);
                }

            } else if ($rowsAffected > 1) {
                $this->exception = print_r(bpfw_error_get(), true) . " bpfwModel.inc.php";
            }

            if (!empty($this->exception)) {
                throw new Exception($this->exception);
            }

            $this->clearCache();

        }

        return true;

    } // always ignore on import/export

    /**
     * Summary of AddEntry
     * @param array|DbSubmitValue[] $data keyvalue or data array or DbModelEntry or array of values
     * @param bool $ignoreRightsManagement
     * @param bool $ignoreConversion
     * @param bool $temptable
     * @return int|null
     * @throws Exception
     */
    public function AddEntry(array $data, bool $ignoreRightsManagement = false, bool $ignoreConversion = false, bool $temptable = false): ?int
    {

        if (!$ignoreRightsManagement && !bpfw_hasPermission($this->minUserrankForAdd)) {
            throw new Exception("add not allowed");
        }

        $dataNormalized = $this->CreateDbSubmitvalueArrayFromKeyValueArray($data);

        $dbmodel = $this->getDbModel();

        foreach ($dataNormalized as $k => $v) {

            $val = $v;

            /*if(is_object($v) && get_class($v) == "DbSubmitValue" ){
                $val=$v->data;
            }else{
                $val = $v;
            }*/

            $component = bpfw_getComponentHandler()->getComponent($dbmodel[$k]->display);
            $errorArray = $component->validateValue(array(), "add", $val, $dbmodel[$k], $dataNormalized, $k, $this);


            if (!empty($errorArray)) {
                foreach ($errorArray as $error) {
                    if ($dbmodel[$k]->display == "hidden") {
                        bpfw_error_add($k . " - " . $error, array("type" => "formvalidation", "value" => $val, "page" => $dbmodel[$k]->addpage));
                    } else {
                        bpfw_error_add($error, array("type" => "formvalidation", "key" => $k, "value" => $val, "page" => $dbmodel[$k]->addpage));
                    }

                    // hidden
                    //

                }
            }

        }

        if (!bpfw_error_hasErrors()) {

            $newId = $this->DbInsert($dataNormalized, null, $ignoreConversion, $temptable); //getorpost("temptable"));

            if ($newId === NULL) {
                $this->exception = "Add failed";
            }

            if (!empty($this->exception)) {
                throw new Exception($this->exception);
            }


            if ($temptable) {

                $dialogid = getorpost("dialogid");

                if (!empty($dialogid)) {

                    $key = new DatabaseKey("dialogid", $dialogid, 's');

                    bpfw_getDb()->makeUpdate(array("rowkey" => new DbSubmitValue("rowkey", $newId, bpfw_createModelByName("tempmodellist"))), "tempmodellist", $key);
                    //bpfw_getDb()->makeQuery("update tempmodellist set  rowkey = '$newid' where dialogid = '$dialogid');
                }


            }

            if (!$temptable) {

                unset($dataNormalized["password"]);

                $metadata = json_encode($dataNormalized);

                bpfw_logDataEntry($this->GetTableName(), $newId, "insert", $metadata);
            }

            return $newId;
        }

        return NULL;

    }

    /**
     * @throws Exception
     * @noinspection PhpUnused
     */
    function createModellistAddEntry($tempid)
    {

        if ($tempid >= 0) {
            throw new Exception("createModellistAddEntry id must be negative");
        }

        //  $db->makeQuery("insert into ".$this->GetTableName()."(".$this->tryGetKeyName().")"."VALUES"."(".$tempid.")".";");
        $values = array($this->tryGetKeyName() => $tempid);

        $this->DbInsert($values, bpfw_getDb(), true);  // TODO: default values ber�cksichtigen beim insert w�rde sinn machen


    }

    /**
     * @throws Exception
     */
    function hasMultiplePages($editmode = true): bool
    {

        return $this->getMaxPage($editmode) > 1;

    }

    /**
     * @throws Exception
     */
    function getMaxPage($editmode = true, $ignoreMinimumMaxTabs = false): int
    {


        $lastpage = 0;
        foreach ($this->getDbModel() as $field) {

            if (!$editmode) {
                if (!$field->hiddenOnAdd) {
                    if ($lastpage < $field->addpage) {
                        $lastpage = $field->addpage;
                    }
                }
            } else {
                if (!$field->hiddenOnEdit) {
                    if ($lastpage < $field->editpage) {
                        $lastpage = $field->editpage;
                    }
                }
            }

        }


        if (!$ignoreMinimumMaxTabs) {
            if ($this->getMinTabs($editmode) > $lastpage) {
                $lastpage = $this->getMinTabs($editmode);
            }
        }

        return $lastpage;


    }

    function getMinTabs($editmode): int
    {

        if ($editmode && $this->minimumMaxTabsEdit != -1) {
            return $this->minimumMaxTabsEdit;
        }

        if (!$editmode && $this->minimumMaxTabsAdd != -1) {
            return $this->minimumMaxTabsAdd;
        }

        return $this->minimumMaxTabs;

    }

    function getTabClasses(bool $editMode, int $pageID): string
    {
        return "";
    }

    function getTabName($editMode, $pageID): string
    {
        return "Seite " . $pageID;
    }

    function filter_limitToUserIfNoAdmin($where, $count, $offset, $sort, $join)
    {

        if (!bpfw_isAdmin()) {
            $limitToUserid = bpfw_getUserId();
            $where = "(" . $where . ")" . " AND userId = '$limitToUserid'";
        }

        return $where; // was: join findme

    }

}