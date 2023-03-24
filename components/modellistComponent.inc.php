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
 * checkboxComponent short summary.
 *
 * checkboxComponent description.
 *
 * @version 1.0
 * @author torst
 */
class ModellistComponent extends DefaultComponent
{


    function __construct($name, $componentHandler)
    {
        parent::__construct($name, $componentHandler);
        $this->defaultSortable = false;
    }

    function getHeaderJs(): string
    {

        ob_start();

        ?>

        <script>

            /*
            function gotModellistDatatableData(d) {

                alert(JSON.stringify(this));

                //alert(jQuery(this).api.settings);
                //  activePage = getQueryVariable("p");

                d.ajaxCall = true;
                d.command = "getModellistcomponentDataEntries";
                d.dataModel = jQuery(this).data("model");
                d.temptable = jQuery(this).data("temptable");

                if (typeof modellistcomponentAjaxDataFilter === "function") {
                    d = modellistcomponentAjaxDataFilter(d);
                }

            }
            */

        </script>

        <?php

        return ob_get_clean();

    }


    /**
     * @throws Exception
     */
    function getRedrawJs(): string
    {


        ob_start();

        ?>

        <script>


            jQuery(document).ready(function () {


                jQuery(".datatable_component").each(function () {


                    let order = [];

                    if (jQuery(this).data("defaultsort") != null && jQuery(this).data("defaultsort") !== "" &&
                        jQuery(this).data("defaultsortorder") != null && jQuery(this).data("defaultsortorder") !== "") {
                        order = [jQuery(this).data("defaultsort"), jQuery(this).data("defaultsortorder")];
                    }


                    if (!jQuery.fn.DataTable.isDataTable(this)) // check if already initialized
                    {

                        const modelname = jQuery(this).data("model");
                        const fieldname = jQuery(this).data("fieldname");
                        let filterid = jQuery(this).data("filter"); // TODO: anders bei fester id

                        const temptable = jQuery(this).data("temptable") ? "1" : "0"; // TODO: anders bei fester id

                        const searching = jQuery(this).data("enable_search");
                        const paging = jQuery(this).data("enable_filter");
                        //var sorting = jQuery(this).data("enable_sorting");

                        const parentmodel = jQuery(this).data("parentmodel");

                        const defaultlength = jQuery(this).data("defaultlength");


                        if (filterid === "formid") {
                            //let currentDialogId = getIdOfCurrentDialog(); // TODO check if this works
                            filterid = jQuery(this).data("parentid");
                            //alert(id);
                            //((alert(currentDialogId + "asdf findme");
                            //filterid = jQuery("#" + currentDialogId).data("rowid");
                        }

                        // alert(filterid);

                        const tablejquery = jQuery(this);

                        let lang;

                        <?php if (bpfw_getCurrentLanguageCode() == "de") { ?>
                        lang = {
                            "url": bpfw_bpfwpath()+"libs/datatables/German.json"
                        };
                        <?php } ?>

                        const table = jQuery(this).DataTable(
                            {


                                "searching": searching,
                                "paging": paging,

                                iDisplayLength: defaultlength,

                                searchDelay: 750,

                                "columnDefs": [{
                                    "targets": 'no-sort',
                                    "orderable": false,
                                }],

                                "language": lang,

                                responsive: true,


                                "order": order,

                                "processing": false,
                                "serverSide": true,

                                "ajax": {

                                    "url": "",
                                    "data": function (d) {

                                        d.modelname = modelname;
                                        d.fieldname = fieldname;
                                        d.filter = filterid;
                                        d.temptable = temptable;
                                        d.p = parentmodel;

                                        d.ajaxCall = true;
                                        d.command = "getModellistcomponentDataEntries";

                                        if (typeof modellistcomponentAjaxDataFilter === "function") { // optional data Filter
                                            const newD = modellistcomponentAjaxDataFilter(d);

                                            d.modelname = newD.modelname;
                                            d.fieldname = newD.fieldname;
                                            d.filter = newD.filter;
                                            d.temptable = newD.temptable;
                                            d.p = newD.p;

                                            d.ajaxCall = newD.ajaxCall;
                                            d.command = newD.command;
                                        }

                                    }

                                },

                                "drawCallback": function (settings) {
                                    // alert('DataTables has redrawn the table');
                                    // adminTablesAjax.responsive.recalc();
                                }
                            }
                        );

                        table.on('draw', function () {


                            table.columns.adjust()
                                .responsive.recalc();

                            // alert("ok");


                        });


                        table.on("click", "tbody tr > td:nth-child(2)", function () {

                            const id = jQuery(this).find("div").data("id");
                            // alert(id);
                            //toggleTableMenu(id)

                            // var preventDefault = toggleTableMenu(id, table);


                            const isOpen = jQuery(this).find(".list-button-tablefield[data-id=" + id + "] .datalist_button_wrapper").hasClass("list-enabled");

                            tablejquery.find(".list-button-tablefield .datalist_button_wrapper").removeClass("list-enabled");

                            if (!isOpen) {
                                jQuery(this).find(".list-button-tablefield[data-id=" + id + "] .datalist_button_wrapper").addClass("list-enabled");
                            }

                            return !isOpen;

                            //if (preventDefault) {
                            //e.preventDefault();
                            //}

                        });

                    }

                });


            });

        </script>

        <?php

        return ob_get_clean();

    }


    function getCss(): string
    {
        return "";
    }

    public function GetDisplayLabelHtml(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, int|string $rowKey, BpfwModel $model): string
    {

        ob_start();
        echo $this->getRowValue($value, $fieldName, $fieldDbModel, $rowKey, $model);
        return ob_get_clean();

    }

    /**
     * get row Value for ModelList - similar to getRowValue of Tableview
     * @param mixed $value
     * @param string $fieldName
     * @param BpfwModelFormfield $fieldDbModel
     * @param int|string $rowkey
     * @param BpfwModel $model
     * @return string
     * @throws Exception
     */
    function getRowValue(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, int|string $rowkey, BpfwModel $model): string
    {

        $model2 = bpfw_createModelByName($fieldDbModel->datamodel);

        // echo $model->GetTableName();

        $filtername = $fieldDbModel->datamodel_filtername;

        if ($filtername == "primaryKeyOfParent") {
            $pk = $model->tryGetKeyName();
        }

        $listfields = $fieldDbModel->datamodel_fields;
        if (empty($listfields) || $listfields == "default") {
            $listfields = $model2->getFieldnamesForListFromDbModelOnly();
        }

        // TODO: das geht ggf. optimierter als ein query pro Zeile

        $listfieldname = $fieldDbModel->datamodel_listfield;

        if ($listfieldname == "first_entry") {
            $listfieldname = current($listfields);
        }

        return bpfw_generateKeyvaluestringFromDbfield($model2, $listfieldname, $model->tryGetKeyName(), $rowkey);

    }

    /**
     * @throws Exception
     */
    protected function displayAsAdd(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, BpfwModel $model): string
    {
        if($fieldDbModel->datamodel_filter == "formid") {
            // using temptable
            return  $this->displayAsEdit($value, $fieldName, $fieldDbModel, $model, 0, true);
        }else{
            return  $this->displayAsEdit($value, $fieldName, $fieldDbModel, $model, 0);
        }

    }

    /**
     * @throws Exception
     */
    protected function displayAsEdit(mixed $value, string $fieldName, BpfwModelFormField $fieldDbModel, BpfwModel $model, string|int $rowKey, $temptable = false): string
    {

        // TODO: check
        //$temptable = $rowKey == 0; // $fieldDbModel->datamodel_filter == "formid" && $rowKey == 0; //|| getorpost("temptable") || $temptable;
            // getorpost("temptable");  // TODO über Parameter?

        $temptable = $temptable || getorpost("temptable");

        ob_start();

        if ($temptable) {

            // create something to identify temptable
            require_once(BPFW_MVC_PATH . "models/tempmodellistModel.inc.php");

            $filtername = $fieldDbModel->datamodel_filtername;

            if ($filtername == "primaryKeyOfParent") {
                $filtername = $model->tryGetKeyName();
            }

            $filter = getorpost("filter");

            $existingEntry = null;

            if ($rowKey != 0) {
                $existingEntry = TempmodellistModel::getInstance()->DbSelect(" rowkey = '" . $rowKey . "' AND parentid = '" . $filter . "' and fieldname = '" . $fieldName . "' AND model = '" . $model->GetTableName() . "' AND layer0identifier = '" . getorpost("layer0identifier") . "'");
            }

            if (!empty($existingEntry)) {
                // set key to previous value, so that we can leave and reedit nested modellists
                $rowKey = current($existingEntry)["modellistId"];
            } else {
                $newentry = TempmodellistModel::getNewEntry($fieldName, getorpost("parentformidentifier"), $model->GetTableName(), $fieldDbModel->datamodel, $filtername, getorpost("layer0identifier"), $filter, $rowKey);
                $rowKey = $newentry;
            }

        }


        $addbuttonclasses = "modellistbutton new-entry-button add-button";

        ?>

        <?php if ($fieldDbModel->datamodel_show_add) { ?>

        <div style="text-align:right;border:0;float:right;">
            <div id="addbutton_modellist_<?php echo $fieldName; ?>" data-parentid='<?php echo $rowKey; ?>'
                 data-parentmodel='<?php echo $model->GetTableName(); ?>'
                 data-temptable='<?php echo (int)$temptable; ?>'
                 data-model="<?php echo $fieldDbModel->datamodel; ?>" class="<?php echo $addbuttonclasses ?>" style=""
                 data-filterid="<?php echo $temptable ? $rowKey : $fieldDbModel->datamodel_filter; ?>">
                <i class="topbuttonicon fa fa-plus"></i>
                <div><?php echo __("New entry"); ?></div>
            </div>
        </div>

    <?php } ?>

        <div class="modellist_wrapper">

            <?php


            /*var searching = jQuery(this).data("enable_sort");
            var paging = jQuery(this).data("enable_filter");
            var sorting = jQuery(this).data("enable_sorting");

            var defaultlength = jQuery(this).data("defaultlength");*/

            $datamodel_enable_search = $fieldDbModel->datamodel_enable_search ? "true" : "false";
            $datamodel_enable_filter = $fieldDbModel->datamodel_enable_filter ? "true" : "false";
            $datamodel_enable_sorting = $fieldDbModel->datamodel_enable_sorting ? "true" : "false";
            $datamodel_defaultlength = (int)$fieldDbModel->datamodel_defaultlength > 0 ? (int)$fieldDbModel->datamodel_defaultlength : -1;

            $datamodel_show_header = $fieldDbModel->datamodel_show_header ? "true" : "false";


            $sort = "";


            if ($fieldDbModel->datamodel_enable_sorting && !empty($fieldDbModel->datamodel_sortfield) && is_numeric($fieldDbModel->datamodel_sortfield)) {

                $sort = ' data-defaultsort="' . $fieldDbModel->datamodel_sortfield . '" data-defaultsortorder="' . $fieldDbModel->datamodel_sortorder . '"';

            }


            $tabledata = "data-parentid='" . $rowKey . "' data-parentmodel='" . $model->GetTableName() . "'  data-enable_search='" . $datamodel_enable_search . "' data-enable_filter='" . $datamodel_enable_filter . "' data-enable_sorting='" . $datamodel_enable_sorting . "' data-defaultlength='" . $datamodel_defaultlength . "' ";

            $tableid = "tablecomponent_table_" . time() . "_" . rand();


            ?>
            <style>
                <?php

                if(!$fieldDbModel->datamodel_show_header){ ?>

                <?php echo "#$tableid"; ?>
                thead {
                    /*position: absolute !important;
                    top: -9999px !important;
                    left: -9999px !important;*/
                    display: none;
                }


                <?php
        }

                ?>


                <?php if(!$fieldDbModel->datamodel_show_modification_buttons){
                ?>
                #
                <?php echo $tableid; ?>
                .datalist_button_wrapper {
                    display: none !important;
                }


                #
                <?php echo $tableid; ?>
                th[data-name='bpfw_datalist_buttons'] {
                    display: none;
                }

                #
                <?php echo $tableid; ?>
                > tbody > tr > td:nth-child(1) {
                    display: none !important;
                }


                <?php } ?>


                <?php if(!$fieldDbModel->datamodel_show_edit || !$fieldDbModel->datamodel_show_modification_buttons){
                                     ?>
                #
                <?php echo $tableid; ?>
                .edit-button {
                    display: none !important;
                }

                <?php } ?>


                <?php if(!$fieldDbModel->datamodel_show_add || !$fieldDbModel->datamodel_show_modification_buttons){
                ?>
                #
                <?php echo $tableid; ?>
                .add-button {
                    display: none !important;
                }

                <?php } ?>

                <?php if(!$fieldDbModel->datamodel_show_delete || !$fieldDbModel->datamodel_show_modification_buttons){
                ?>
                #
                <?php echo $tableid; ?>
                .delete-button {
                    display: none !important;
                }

                <?php } ?>

                <?php if(!$fieldDbModel->datamodel_show_duplicate || !$fieldDbModel->datamodel_show_modification_buttons){
                ?>
                #
                <?php echo $tableid; ?>
                .duplicate-button {
                    display: none !important;
                }

                <?php } ?>

            </style>


            <table id="<?php echo $tableid; ?>" <?php echo $tabledata; ?> <?php echo $sort; ?>
                   style="min-width:calc(100% - 20px);width:calc(100% - 20px);"
                   class="datatable datatable_component datamodel_component ModellistComponent"
                   data-fieldname="<?php echo $fieldName; ?>" data-model="<?php echo $fieldDbModel->datamodel; ?>"
                   data-filter="<?php echo $temptable ? $rowKey : $fieldDbModel->datamodel_filter; ?>"
                   data-temptable="<?php echo (int)$temptable; ?>">


                <?php
                //if($hvalue->ajax_sortable === false || $hvalue->ajax_sortable === null && !$componentinfo->defaultSortable){
                // $cssClasses = "no-sort";
                // }
                ?>

                <thead>

                <th data-name='bpfw_datalist_empty' class="no-sort"></th>
                <th data-name='bpfw_datalist_buttons'
                    class='<?php if (!$fieldDbModel->datamodel_enable_sorting) echo "no-sort"; ?>'></th>

                <?php

                $filter = $fieldDbModel->datamodel_filter;
                if ($filter == "formid") $filter = getorpost("rowid");
                $model_obj = ModelLoader::findAndCreateModel($fieldDbModel->datamodel, $filter); // add Components ob this list

                if (empty($model_obj)) {
                    throw new Exception("invalid model or no permission");
                }

                $listfields = $fieldDbModel->datamodel_fields;
                if (empty($listfields) || $listfields == "default") {
                    $listfields = $model_obj->getFieldnamesForListFromDbModelOnly();
                }

                foreach ($listfields as $hkey) {

                    $fielddata = $model_obj->findDbField($hkey);

                    if ($fielddata == null) {
                        throw new Exception("modellist error: field '$hkey' is empty/not existing in model:" . $fieldDbModel->datamodel . " fields requeested: " . print_r($fieldDbModel->datamodel_fields, true));
                    }

                    $cssClasses = "";


                    $componentinfo = bpfw_getComponentHandler()->getComponent($fielddata->display);

                    if (empty($fieldDbModel) || !$fieldDbModel->datamodel_enable_sorting ||
                        $fielddata->ajax_sortable === false || $fielddata->ajax_sortable === null && !$componentinfo->defaultSortable) {
                        $cssClasses .= "no-sort";
                    }

                    $newlabel = $fielddata->label;

                    if (!empty($fielddata->list_spoiler_hint)) {

                        $newlabel = bpfw_addSpoilerHint($newlabel, $fielddata->list_spoiler_hint);
                    }

                    $headerfield = "<th class='$cssClasses' data-name='$hkey' style='white-space:nowrap'>" . $newlabel . "</th>";

                    echo $headerfield;

                }

                ?>

                </thead>

                <tbody></tbody>


            </table>

        </div>
        <?php

        return ob_get_clean();

    }

}