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
class DefaultControl
{

    /**
     * Summary of $model
     * @var BpfwModel
     */
    var mixed $model = null;
    var ?int $addedKey = null;


    /** @noinspection PhpMissingReturnTypeInspection */

    function __construct($model = null)
    {
        $this->model = $model;
    }

    function setModel($model)
    {
        $this->model = $model;
    }

    function isDeleteAction(): bool
    {
        return bpfw_isDeleteAction();
    }

    function isAddAction(): bool
    {
        return bpfw_isAddAction();
    }

    /** @noinspection PhpUnused */

    function isEditAction(): bool
    {
        return bpfw_isEditAction();
    }

    function isDuplicateAction(): bool
    {
        return bpfw_isDuplicateAction();
    }

    /**
     * is the user makeing an update, delete or create action?
     * @return boolean
     */
    function makingCUDAction(): bool
    {

        if (getorpost('openAdd') != NULL || getorpost('edit') != NULL || isset($_GET['delete']) && is_numeric($_GET['delete'])) {

            return true;

        } else return false;

    }

    function handleActions(): void
    {

        global $database;

        try {

            if (isset($_POST['addAction'])) {

                //  var_dump(plainKvpFromDataArray($model->submitValues));
                ///$newid = $model->DbInsert(plainKvpFromDataArray($model->submitValues));


                // $model->error = $database -> InsertCityData($model->submitValues);


                $this->addedKey = $this->model->AddEntry($this->model->submitValues);

                $this->model->clearCache();

            } else if (isset($_POST['editAction'])) {
                //var_dump($model->submitValues);
                ///$model->DbUpdate(plainKvpFromDataArray($model->submitValues));

                //$model->error = $database -> EditCityData($model->submitValues);
                $this->model->EditEntry($this->model->submitValues);
                $this->model->clearCache();

            }/*else if( isset($_GET['delete']) && is_numeric($_GET['delete']) )
            {
                // $model->error = $database -> DeleteCityData($_GET['delete']);

                $this->model->DeleteEntry($_GET['delete']);
                $this->model->clearCache();
            }*/ else
                if (getorpost("command") != null) {
                    $this->handleAjaxCommand(getorpost("command"));

                }

        } catch (Exception $ex) {

            echo "<b>handleActions Exception</b>:<br><pre>$ex</pre>";
            die();

            // throw new Exception($ex);

        }

    }

    /**
     * @throws Exception
     */
    function handleAjaxCommand(string $command): void
    {

        // echo "handleAjaxCommand" . get_class($this);

        if ($command == "getAddTitle") {
            if (method_exists($this->model->view, "getAddTitle")) {
                echo json_encode($this->model->view->getAddTitle());
            } else {
                echo __("Add entry");
                //throw new \Exception("getAddTitle not existing in view");
            }

        } else if ($command == "getEditTitle") {

            $id = getorpost("id");

            if (method_exists($this->model->view, "getEditTitle")) {
                echo json_encode($this->model->view->getEditTitle($id));
            } else {
                echo __("Edit entry");
                //throw new \Exception("getEditTitle not existing in view");
            }

        } else if ($command == "getDeleteTitle") {

            $id = getorpost("id");

            if (method_exists($this->model->view, "getDeleteTitle")) {
                echo json_encode($this->model->view->getDeleteTitle($id));
            } else {
                echo __("Delete entry");
                //throw new \Exception("getDeleteTitle not existing in view");
            }

        } else if ($command == "getDuplicateTitle") {

            $duplicate = getorpost("duplicate");
            if (method_exists($this->model->view, "getDuplicateTitle")) {

                echo json_encode($this->model->view->getDuplicateTitle($duplicate));
            } else {
                echo __("Duplicate entry");
                //throw new \Exception("getDuplicateTitle not existing in view");
            }

        } else if ($command == "getEntry") {

            $id = getorpost("id");
            if (!empty($id) || $id === '0') {
                echo json_encode($this->model->getEntry($id, 'default', -1, 0, '', !empty(getorpost("temptable")) && getorpost("temptable") != "false" && getorpost("temptable") != "0"));
            } else {
                throw new Exception("getEntry not set: id");
            }

        } else if ($command == "getEditEntryHtml") {

            $id = getorpost("id");

            if (!empty($id) || $id === '0') {


                //   $this->model->getEntry($id, ' 1', -1, 0, '', getorpost("temptable"));


                if (method_exists($this->model->view, "renderEditModalContent")) {
                    $this->model->view->renderEditModalContent($id, getorpost("parentformidentifier"));
                } else {
                    throw new Exception("renderEditModalContent not existing in view");
                }


                // echo json_encode($this->model->getEntry($id));

            } else {
                throw new Exception("getEntry not set: id");
            }

        } else if ($command == "getAddEntryHtml") {
            if (method_exists($this->model->view, "renderAddModalContent")) {

                $this->model->view->renderAddModalContent(getorpost("parentformidentifier"));

            } else {
                throw new Exception("renderAddModalContent not existing in view");
            }

        } else if ($command == "createAddDialog") {


            $this->model->view->addMode = true;
            $this->model->view->editmode = false;

            $settings = $this->getDialogSettings();
            $settings["html"] = $this->model->view->createAddDialog($settings["css_id"], $settings["layer"], $settings["rowid"], "modal-lg modal-xlg", $settings["temptable"]);
            echo json_encode($settings);


        } else if ($command == "createEditDialog") {

            $this->model->view->editmode = true;
            $this->model->view->addMode = false;

            $settings = $this->getDialogSettings();

            if (method_exists($this->model->view, "createEditDialog")) {
                $settings["html"] = $this->model->view->createEditDialog($settings["css_id"], $settings["layer"], $settings["rowid"], "modal-lg modal-xlg", $settings["temptable"]);
            } else {
                throw new Exception("Model for " . getorpost("p") . " not created, please reload page." . json_encode($_SESSION) . " /// " . json_encode($_POST));
            }

            echo json_encode($settings);

        } else if ($command == "createIFrameDialog") {

            $settings = array();

            $settings["css_id"] = $this->model->createDialogId();
            $settings["layer"] = getorpost("hierachy");
            $settings["rowid"] = getorpost("rowid");
            $settings["iframeurl"] = getorpost("iframeurl");

            if (!is_numeric($settings["layer"])) $settings["layer"] = 0;

            $settings["html"] = $this->model->view->createIFrameDialog($settings["css_id"], $settings["layer"], $settings["iframeurl"]);

            echo json_encode($settings);

        } else if ($command == "AddEntrySubmit") {

            // TODO: ggf. das falsche Model?

            $temptable = getorpost("temptable") == 1;

            $this->addedKey = $this->model->AddEntry($this->model->submitValues, false, false, getorpost("temptable") == 1);

            $dialogid = getorpost("dialogid");

            if(!bpfw_error_hasErrors()) {
                if (!empty($dialogid)) {

                    // require_once(BPFW_MVC_PATH."models/tempmodellistModel.inc.php");

                    if (!$temptable && $this->addedKey !== null && $this->addedKey > 0) {

                        // schauen, ob tempmodeleinträge vorhanden sind und diese dann zu echten Einträgen machen.
                        $this->checkAndCopyTemptables($dialogid, $this->addedKey);


                    }
                }

            } else {

                $errors = array();
                foreach (bpfw_error_get() as $error) {
                    $errors[] = $error;
                }
                echo json_encode($errors);

                /*
                echo "<ul>";

                $first = true;

                foreach(bpfw_error_get() as $error){
                    echo "<li>";
                    echo $error->msg;
                    echo "</li>";
                    $first = false;
                }

                echo "</ul>";*/

            }

        } else if ($command == "deleteEntry") {

            if (!empty($_GET['delete'])) {

                $deleteid = $_GET['delete'];

                $this->model->DeleteEntry($deleteid, empty(getorpost("temptable")) ? 0 : (int)getorpost("temptable"));

                if (bpfw_error_hasErrors()) {

                    $errors = array();
                    foreach (bpfw_error_get() as $error) {
                        $errors[] = $error;
                    }
                    echo json_encode($errors);

                    /*
                    echo "<ul>";
                    $first = true;

                    foreach(bpfw_error_get() as $error){
                        echo "<li>";
                        echo $error->msg;
                        echo "</li>";
                        $first = false;
                    }

                    echo "</ul>";*/

                }

            }


        } else if ($command == "EditEntrySubmit") {

            // $formdata = $_POST;

            // $files = $_FILES;

            $temptable = getorpost("temptable") == 1;

            $this->model->EditEntry($this->model->submitValues, false, false, $temptable);

            if (bpfw_error_hasErrors()) {

                $errors = array();
                foreach (bpfw_error_get() as $error) {
                    $errors[] = $error;
                }
                echo json_encode($errors);

                /*
                echo "<ul>";
                $first = true;

                foreach(bpfw_error_get() as $error){
                    echo "<li>";
                    echo $error->msg;
                    echo "</li>";
                    $first = false;
                }

                echo "</ul>";*/

            }

        } else if ($command == "getModellistcomponentDataEntries" || $command == "getDatatableEntries") { // TODO: gehört eigentlich von defaultcontrol getrennt... in defaultdatacontrol oder sowas . . .

            /*
              array(9) {
              ["p"]=>
              string(8) "location"
              ["draw"]=>
              string(1) "1"
              ["columns"]=>
              array(5) {
                [0]=>
                array(5) {
                  ["data"]=>
                  string(1) "0"
                  ["name"]=>
                  string(0) ""
                  ["searchable"]=>
                  string(4) "true"
                  ["orderable"]=>
                  string(4) "true"
                  ["search"]=>
                  array(2) {
                    ["value"]=>
                    string(0) ""
                    ["regex"]=>
                    string(5) "false"
                  }
                }
                [1]=>
                array(5) {
                  ["data"]=>
                  string(1) "1"
                  ["name"]=>
                  string(0) ""
                  ["searchable"]=>
                  string(4) "true"
                  ["orderable"]=>
                  string(4) "true"
                  ["search"]=>
                  array(2) {
                    ["value"]=>
                    string(0) ""
                    ["regex"]=>
                    string(5) "false"
                  }
                }
                [2]=>
                array(5) {
                  ["data"]=>
                  string(1) "2"
                  ["name"]=>
                  string(0) ""
                  ["searchable"]=>
                  string(4) "true"
                  ["orderable"]=>
                  string(4) "true"
                  ["search"]=>
                  array(2) {
                    ["value"]=>
                    string(0) ""
                    ["regex"]=>
                    string(5) "false"
                  }
                }
                [3]=>
                array(5) {
                  ["data"]=>
                  string(1) "3"
                  ["name"]=>
                  string(0) ""
                  ["searchable"]=>
                  string(4) "true"
                  ["orderable"]=>
                  string(4) "true"
                  ["search"]=>
                  array(2) {
                    ["value"]=>
                    string(0) ""
                    ["regex"]=>
                    string(5) "false"
                  }
                }
                [4]=>
                array(5) {
                  ["data"]=>
                  string(1) "4"
                  ["name"]=>
                  string(0) ""
                  ["searchable"]=>
                  string(4) "true"
                  ["orderable"]=>
                  string(4) "true"
                  ["search"]=>
                  array(2) {
                    ["value"]=>
                    string(0) ""
                    ["regex"]=>
                    string(5) "false"
                  }
                }
              }
              ["start"]=>
              string(1) "0"
              ["length"]=>
              string(3) "100"
              ["search"]=>
              array(2) {
                ["value"]=>
                string(0) ""
                ["regex"]=>
                string(5) "false"
              }
              ["ajaxCall"]=>
              string(4) "true"
              ["command"]=>
              string(19) "getDatatableEntries"
              ["_"]=>
              string(13) "1561641139573"
            }

            */

            // https://datatables.net/manual/server-side


            $start = getorpost("start");
            $length = getorpost("length");
            $draw = (int)getorpost("draw");
            $search = getorpost("search");
            $columns = getorpost("columns");

            $temptable = getorpost("temptable") == 1;

            $order = getorpost("order");

            $join = "";
            $joinforcount = "";

            if (
                !isset($start) ||
                !isset($length) ||
                !isset($search) || !is_array($search) ||
                !isset($columns) || !is_array($columns)
            ) {
                throw new Exception("values not set as required");
            }

            $response = array();

            $response["draw"] = $draw;

            $response["error"] = "";

            $response["data"] = array();

            $model = $this->model;
            $dbmodel = $this->model->getDbModel();
            $isEmptyModel = empty($dbmodel);


            if ($command == "getModellistcomponentDataEntries") {

                // todo: Model wechseln und ggf. Daten laden
                // $dbmodel = ... modelloader, laden ...

                /*echo "<pre>";
                var_dump($_POST);
                var_dump($_GET);
                echo "</pre>";*/

                if (empty(getorpost("fieldname"))) {
                    throw new Exception("fieldname not existing or no permission");
                }

                $fieldname = getorpost("fieldname");

                $field = $this->model->findDbField($fieldname);

                if (empty($field)) {
                    throw new Exception("field not found $fieldname in " . $this->model->GetTableName());
                }

                $modelname = $field->datamodel;

                $dbmodel_class = ModelLoader::findAndCreateModel($modelname);
                if (empty($dbmodel_class)) {
                    throw new Exception("invalid model class");
                }

                $model = $dbmodel_class;
                $dbmodel = $dbmodel_class->getDbModel();

                $viewloader = new ViewLoader();
                $model->view = $viewloader->createView($model);

                $model->isRootHierachy = false;

            }

            $queryorder = array();

            $sort = "";

            if (!$isEmptyModel && is_array($order)) {

                foreach ($order as $orderentry) {

                    $columnid = $orderentry["column"];
                    $column = $columns[$columnid];
                    $fieldname = $column["name"];
                    $direction = $orderentry["dir"];

                    if (isset($dbmodel[$fieldname])) {
                        $queryorder[] = new DatabaseSortEntry($fieldname, $direction);
                        $sort = $fieldname;
                    } else {
                        if ($fieldname != "bpfw_datalist_buttons") {
                            $response["error"] = "Sort: non existing column $fieldname";
                        } else {
                            $fieldname = $model->getKeyName();
                            $queryorder[] = new DatabaseSortEntry($fieldname, $direction);
                            $sort = $fieldname;
                        }

                    }

                }

            }

            $where = ""; // " WHERE 1 ";

            $db = bpfw_getDb();

            $searchtxt = $db->escape_string($search["value"]);

            if (!empty($searchtxt)) {

                $searchTypes = getorpost("searchType");


                // no index existing or limited search, realtimesearch
                if (!$model->createSearchIndex || !empty($searchTypes) && !in_array("other", $searchTypes)) {

                    if (empty($where)) {
                        $where = " 1 "; // $filteredwhere; //" where 1 ";
                    }

                    $first = true;

                    foreach ($dbmodel as $dbkey => $entry) {

                        if (!$entry->is_searchable) continue;

                        if ($this->checkSearchField($dbkey, $entry, $searchTypes)) {

                            if ($entry->type->isSearchableType()) {

                                if ($first) {
                                    $first = false;
                                    $where .= " and(";
                                } else {
                                    $where .= " OR ";
                                }

                                $where .= $model->GetTableName() . "." . $dbkey;
                                $where .= " LIKE ";
                                $where .= "'%" . $searchtxt . "%'";

                            }

                        }

                    }

                    $joinforcount = $join;

                    // sortsearch columns

                    foreach ($dbmodel as $dbkey => $entry) {

                        if (!$entry->is_searchable) continue;

                        if ($this->checkSearchField($dbkey, $entry, $searchTypes)) {

                            if (!empty($entry->sortsearch)) {


                                if (!$first) {
                                    $where .= " OR ";
                                } else {
                                    $where .= " and(";
                                }

                                $where .= $entry->sortsearch->field;
                                $where .= " LIKE ";
                                $where .= "'%" . $searchtxt . "%'";


                                if ($dbkey != $sort) {

                                    $join .= $entry->sortsearch->join;
                                }

                                $joinforcount .= $entry->sortsearch->join;


                                $first = false;

                                // echo $dbkey; echo " -> "; echo $where; echo "\r\n";

                            }

                        }

                    }

                    if (!$first)
                        $where .= " ) ";

                } else {

                    $where .= $model->GetTableName() . ".searchindex";
                    $where .= " LIKE ";
                    $where .= "'%" . $searchtxt . "%'";

                }

            }


            if (empty($where))
                $where = "  1 ";


            // TODO: prüfen ob if($command == getModellistcomponentDataEntries) reicht ... sonst evtl doppelt


            //  $join = bpfw_do_filter(BpfwModel::FILTER_ENTRY_SELECT_JOIN, $model->GetTableName(), array($join, $where, $length, $start,$sort), $model);
            //  $where = bpfw_do_filter(BpfwModel::FILTER_ENTRY_SELECT_WHERE, $model->GetTableName(), array($where, $length, $start,$sort,$join), $model);


            $where = $this->filterListSqlWhere($where, $searchtxt, $join);  // apply filter from specific model

            if ($temptable) {
                $model->createTempTableIfNotExists();
            }


            $whereUnfiltered = $this->filterListSqlWhere(" 1 ", "", $join);  // apply filter from specific model

            $response["recordsTotal"] = $model->CountAllEntries($whereUnfiltered, $joinforcount, $temptable);

            if ($response["recordsTotal"] == null) {
                $response["recordsTotal"] = 0;
            }

            if (!empty($searchtxt)) {

                $response["recordsFiltered"] = $model->CountAllEntries($where, $joinforcount, $temptable); // nach Suchfilter Anwendung

            } else {
                $response["recordsFiltered"] = $response["recordsTotal"];
            }

            $data = array();


            if (!$isEmptyModel) {
                $data = $model->GetEntries($where, $length, $start, $queryorder, $join, $temptable);
            }
            $model->CountAllEntries("1", $join, $temptable);

            $data = $this->filterListResponse($data, $response);

            foreach ($data as $key => $dbmodelentry) {

                //$values = $dbmodelentry->GetKeyValueArray(false);

                $newentry = array();
                foreach ($columns as $column) {

                    $name = $column["name"];

                    if ($name == "bpfw_datalist_buttons") {
                        /* fa-ellipsis-v */
                        $button = "<a  onClick='/*return toggleTableMenu(" . $key . ");*/' data-id='$key' title='Menü öffnen' class='expand-button' href='#'><i class='tableicon fa bpfw-openmenu-icon'></i></a>";
                        $buttonhtml = /*get_class($model->view)."(defaultcontrol) ".*/
                            "<div data-id='$key' class='list-button-tablefield'><div class='open-listmenu-button'>" . $button . "</div>" . $model->view->getButtonIconHtml($key, $dbmodelentry, $model, $model->view, $temptable) . "</div>";

                        $newentry[] = $buttonhtml;

                    } else
                        if ($name == "bpfw_datalist_empty") {

                            $html = "";

                            $newentry[] = $html;

                        } else {

                            $hvalue = $dbmodel[$name];
                            $component = bpfw_getComponentHandler()->getComponent($hvalue->display);

                            $rawValue = "";

                            $rawValue = $dbmodelentry->$name ?? "-";

                            $displayContent = $component->GetDisplayLabelHtml($rawValue, $name, $hvalue, $key, $model);

                            $lengthBackup = $hvalue->list_maxlength;
                            $hvalue->list_maxlength = 999;
                            $displayContent_full = $component->GetDisplayLabelHtml($rawValue, $name, $hvalue, $key, $model);
                            $hvalue->list_maxlength = $lengthBackup;

                            $altlabel = "";
                            if ($hvalue->display == "text") {
                                $altlabel = $displayContent_full;
                            }

                            $extraCssClass = "";
                            if ($hvalue->bold_content) {
                                $extraCssClass .= " bold_content ";
                            }

                            // list_background list_border

                            /*const BACKGROUND = "list_background";

const BORDER = "list_border";

const COLOR = "list_color";*/

                            $innerinline = "";


                            $withbox = "";

                            if ($hvalue->list_background) {
                                $innerinline = "background-color:" . $hvalue->list_background . ";";
                                $withbox = "inner_list_wrap_withbox";
                            }

                            if ($hvalue->list_border) {
                                $innerinline .= "border:1px solid " . $hvalue->list_border . ";";
                                $withbox = "inner_list_wrap_withbox";
                            }

                            if ($hvalue->list_color) {
                                $innerinline .= "color:" . $hvalue->list_color . ";";
                            }

                            if ($hvalue->list_background_func) {
                                $innerinline = "background-color:" . ($hvalue->list_background_func)($key, $rawValue, $displayContent) . ";";
                                $withbox = "inner_list_wrap_withbox";
                            }

                            if ($hvalue->list_border_func) {
                                $innerinline .= "border:1px solid " . ($hvalue->list_border_func)($key, $rawValue, $displayContent) . ";";
                                $withbox = "inner_list_wrap_withbox";
                            }

                            if ($hvalue->list_color_func) {
                                $innerinline .= "color:" . ($hvalue->list_color_func)($key, $rawValue, $displayContent) . ";";
                            }


                            $entry = "<div title='" . trim(mb_convert_encoding(trim($altlabel), 'UTF-8', 'UTF-8')) . "' class='defaultlist_wrapper $extraCssClass' data-name='$name' data-rowid='$key' ><div style='$innerinline' class='inner_list_wrap $withbox'>";
                            $entry .= $displayContent;
                            $entry .= "</div></div>";

                            $newentry[] = trim(mb_convert_encoding(trim($entry), 'UTF-8', 'UTF-8'));

                        }

                }

                $response["data"][] = $newentry;

            }

            /*     for($i=0;$i<$length;$i++){



                     $response["data"][] = array("<b>test</b>",$i,3,4,5);
                 }*/

            echo json_encode($response);

            //var_dump($response);
        } else {
            throw new Exception("Command $command not implemented in " . $this->model->GetTableName() . "/" . $this->model->GetTitle());
        }

    }

    /**
     * @return array
     * @throws Exception
     */
    public function getDialogSettings(): array
    {
        $settings = array();

        $settings["css_id"] = $this->model->createDialogId();
        $settings["layer"] = getorpost("hierachy");

        $settings["rowid"] = getorpost("rowid");
        $settings["temptable"] = getorpost("temptable") == null ? 0 : getorpost("temptable");

        if (!is_numeric($settings["layer"])) $settings["layer"] = 0;
        return $settings;
    }

    /** @noinspection PhpUnusedParameterInspection */

    /**
     * @throws Exception
     */
    function checkAndCopyTemptables(string $dialogid, int $new_foreignkey, int $layer = 0): void
    {

        bpfw_log_sql("calling checkAndCopyTemptables($dialogid, $new_foreignkey, $layer = 0)");

        $tempmodel = bpfw_createModelByName("tempmodellist", true); //new TempmodellistModel();

        $entries = $tempmodel->DbSelect(" dialogid = '$dialogid' ");

        //var_dump($entries);

        // bpfw_log_sql("checkAndCopyTemptables $dialogid fknew $new_foreignkey layer $layer rowkeyparent $temprowkey_parent");

        $layer++;

        foreach ($entries as $entry) {

            $modellistid = $entry["modellistId"];
            //$dialogid = $entry["dialogid"];
            //$modelname = $entry["model"];
            $targetmodelname = $entry["targetmodel"];
            //$fieldname = $entry["fieldname"];
            $filtervalue = $entry["filtervalue"];
            //$timestamp = $entry["timestamp"];

            //$layer0identifier = $entry["layer0identifier"];
            //$parentid = $entry["parentid"];
            $rowkey = $entry["rowkey"];

            $modelOfEntry = $this->model->createModelByName($targetmodelname);

            //$tempentrydata = $modelOfEntry->DbSelect($filtervalue." = ".$modellistid, bpfw_getDb(), -1, 0, array(), array(), "", true);


            // select all temporary entries in temptable that was added
            $xtra = "";
            if ($rowkey > 0) {
                //$xtra = "and $rowkey";

                //$parententry = $parentmodel->GetEntry($temprowkey_parent, "default", -1,0,'',true); //$filtervalue." = ".$modellistid.$xtra, bpfw_getDb(), -1, 0, array(), array(), "", true);


            }

            $tempentrydata = $modelOfEntry->DbSelect($filtervalue . " = " . $modellistid . $xtra, bpfw_getDb(), -1, 0, array(), array(), "", true);


            /*if($temprowkey_parent != -1){

                $parentmodel = bpfw_createModelByName($modelname, true);

                $parententry = $parentmodel->GetEntry($temprowkey_parent, "default", -1,0,'',true); //$filtervalue." = ".$modellistid.$xtra, bpfw_getDb(), -1, 0, array(), array(), "", true);

                $keyname = $parentmodel->tryGetKeyName();
                $parentkey = $parententry->$keyname;

                if($parentkey != $temprowkey_parent){
                    continue;
                }

            }*/

            bpfw_log_sql("processing found:" . count($tempentrydata));

            $childstocheck = array();

            if (!empty($tempentrydata)) {

                foreach ($tempentrydata as $temporary_key => $newtempentry) {

                    bpfw_log_sql("tempentry $temporary_key processing");

                    // $temporary_key = $newentry[$modelOfEntry->tryGetKeyName()];

                    unset($newtempentry[$modelOfEntry->tryGetKeyName()]); // PK raus


                    $newtempentry[$filtervalue] = $new_foreignkey; // $this->addedKey;

                    $newkey = $modelOfEntry->DbInsert($newtempentry);


                    // $tempentrydata = $modelOfEntry->DbSelect(parentid." = ".$modellistid, bpfw_getDb(), -1, 0, array(), array(), "", true);


                    // $this->checkAndCopyTemptables();

                    // echo " bearbeite einträge von $modellistid -> $temporary_key <br>";


                    $children = bpfw_getDb()->makeSelect("select modellistId, dialogid, targetmodel, filtervalue, rowkey from tempmodellist where parentid = '$modellistid'"); // $tempmodel->DbSelect(" parentid = '$modellistid' ");
                    if (!empty($children)) {

                        $newentry = array(
                            "newkey" => $newkey,
                            "tempkey" => $temporary_key,
                            "rowdata" => $entry,
                            "children" => $children,
                        );

                        $childstocheck[$newkey] = $newentry;
                    }

                    bpfw_log_sql("children found: " . count($children));

                    $modelOfEntry->DbDeleteByPrimaryKey($temporary_key, 1);
                    $tempmodel->DbDeleteByPrimaryKey($modellistid);


                }


            }


            if (!empty($childstocheck)) {

                //ini_set('xdebug.var_display_max_depth', 10);
                //ini_set('xdebug.var_display_max_children', 256);
                //ini_set('xdebug.var_display_max_data', 1024);

                // var_dump($childstocheck);

                $childstocheck2 = array();

                foreach ($childstocheck as $newkey => $data) {

                    $childstocheck2[$newkey] = $data;
                    $childstocheck2[$newkey]["children"] = array();

                    $children = $data["children"];
                    foreach ($children as $childkey => $child) {

                        /*  $fk_child = $child["targetmodel"];


                        $childmodel = bpfw_createModelByName($child["targetmodel"]);
                        $entries = $tempmodel->DbSelect(" dialogid = '$dialogid' ");
                         */
                        //$childid = $child["modellistId"];


                        bpfw_log_sql("children process: " . $child["rowkey"]);

                        // nur die berücksichtigen, deren fk der modellistid entspricht

                        if ($data["tempkey"] == $child["rowkey"]) {
                            //$this->checkAndCopyTemptables($child["dialogid"], $newkey, $layer);
                            $childstocheck2[$newkey]["children"][$childkey] = $child;
                        }

                    }

                }

                // var_dump($childstocheck2);

                foreach ($childstocheck2 as $newkey => $data) {
                    $children = $data["children"];
                    foreach ($children as $child) {

                        /*  $fk_child = $child["targetmodel"];


                        $childmodel = bpfw_createModelByName($child["targetmodel"]);
                        $entries = $tempmodel->DbSelect(" dialogid = '$dialogid' ");
                         */


                        bpfw_log_sql("children process: " . $child["rowkey"]);

                        // nur die berücksichtigen, deren fk der modellistid entspricht


                        $this->checkAndCopyTemptables($child["dialogid"], $newkey, $layer);


                    }

                }

            }

        }

    }

    /** @noinspection PhpUnusedParameterInspection */

    public function checkSearchField($dbkey, $entry, $searchTypes = array()): bool
    {
        return true;
    }

    /** @noinspection PhpUnusedParameterInspection */

    protected function filterListSqlWhere($where, $searchtxt, $join)
    {
        return $where;
    }

    protected function filterListResponse($data, $response)
    {
        return $data;
    }

}