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
// use UI\Controls\Entry;

/**
 * defaultListView short summary.
 *
 * defaultListView description.
 *
 * @version 1.0
 * @author torst
 */
class DefaultlistView extends DefaultView
{

    const ACTION_DEFAULTLISTVIEW_BEFORE_RENDER_FORM = "defaultlistview_before_render_form";
    const ACTION_DEFAULTLISTVIEW_BEFORE_RENDER_TABLE = "defaultlistview_before_render_table";
    const ACTION_DEFAULTLISTVIEW_DISPLAY_ENTRY = "ACTION_DEFAULTLISTVIEW_DISPLAY_ENTRY";
    const ACTION_DEFAULTLISTVIEW_DISPLAY_BUTTONS = "ACTION_DEFAULTLISTVIEW_DISPLAY_BUTTONS";

    const ACTION_DEFAULTLISTVIEW_BEFORE_RENDER_BUTTONS = "ACTION_DEFAULTLISTVIEW_BEFORE_RENDER_BUTTONS";
    const ACTION_EDITDIALOG_BEFORE_HEADER_CLOSE_ICON = "ACTION_EDITDIALOG_BEFORE_HEADER_CLOSE_ICON";

    const ACTION_RENDER_CUSTOM_TOP_BUTTONS = "ACTION_RENDER_CUSTOM_TOP_BUTTONS";
    /**
     * Summary of $components
     * @var ComponentHandler
     */
    var ComponentHandler $components;
    /**
     * Summary of $entry
     * @var ?DbModelEntry
     */
    var ?DbModelEntry $entry = null;
    /**
     * @var int
     */
    var int $usertype;
    /**
     * @var bool
     */
    var bool $editmode;
    /**
     * @var bool
     */
    var bool $duplicatemode;
    /**
     * @var bool
     */
    var bool $addMode;
    /**
     * @var int
     */
    var int $currentPage;
    /**
     * @var int
     */
    var int $lastPage;
    /**
     * @var boolean
     */
    var bool $isLastPage;
    /**
     * @var boolean
     */
    var bool $hasMultiplePages;
    /**
     * @var bool
     */
    var bool $print = false;
    /**
     * @var bool
     */
    var bool $hideNavigation;
    /**
     * @var string
     */
    var string $filter;
    /**
     * @var bool
     */
    var bool $makeTrash;
    /**
     * @var bool
     */
    var bool $makeEdit;
    /**
     * @var bool
     */
    var bool $makeDuplicate;
    /**
     * @var bool
     */
    var bool $makeSendPassword;
    /**
     * Summary of $hasAnyButtons
     * @var bool
     */
    var bool $hasAnyButtons = false;

    /**
     * Summary of renderView
     * @throws Exception
     */
    function renderView(): void
    {

        $this->renderErrors();

        // render add/edit/duplicate Form
        bpfw_do_action(DefaultlistView::ACTION_DEFAULTLISTVIEW_BEFORE_RENDER_FORM, $this->model->GetTableName());
        $this->renderForm();

        // render Table with entries
        $this->renderTable();

        // register JS
        $this->registerJs();

    }

    /**
     * @throws Exception
     */
    function renderForm()
    {

        if (!$this->editmode) {
            echo '<form autocomplete="off" data-lastpage="' . $this->lastPage . '" id="listviewForm" enctype="multipart/form-data" class="edit-content-form defaultlistview"  action="" method="post" id="addNewElement"';
            if ((!isset ($_GET ['openAdd']) || $_GET ['openAdd'] != "true") &&
                (!isset ($_GET ['duplicate']) || !is_numeric($_GET ['duplicate']))) {
                echo 'style="display:none;"'; // none
            } else {
                echo 'style="display:block;"';
            }
            echo ' >';

        } else {

            echo '<form autocomplete="off"  data-lastpage="' . $this->lastPage . '" id="listviewForm" enctype="multipart/form-data" action="" class="edit-content-form defaultlistview" method="post" id="addNewElement" style="display:block;" >';

        }

        $positions = $this->model->getPositions();

        foreach ($positions as $position) {

            echo "<div class='form_position_" . $position . "' >";

            foreach ($this->model->getDbModel() as $key => $hvalue) {

                $hiddenedit = false;
                $hiddenadd = false;

                if ($hvalue->hiddenOnEdit) {
                    $hiddenedit = true;
                }

                if ($hvalue->hiddenOnAdd) {
                    $hiddenadd = true;
                }


                if (($this->addMode || $this->duplicatemode) && $hiddenadd && $hvalue->default == null) {
                    continue;
                }

                if ($this->editmode && $hiddenedit && $hvalue->default == null) {
                    continue;
                }

                if (!isset ($hvalue->position) || !in_array($hvalue->position, $positions)) {
                    $hvalue->position = POSITION_LEFT;
                }

                if ($hvalue->position != $position) {
                    continue;
                }

                $componentinfo = bpfw_getComponentHandler()->getComponent($hvalue->display);

                $fieldValue = null;

                $rawvalue = null;

                if (array_key_exists($key, $_POST)) { // ( $_POST [$key] )) {
                    $rawvalue = $_POST [$key];
                } else if ($this->entry != null && property_exists($this->entry, $key)) {
                    $rawvalue = $this->entry->$key;
                } else if ($hvalue->default !== "") {
                    $rawvalue = $hvalue->default;
                }

                if ($this->duplicatemode) {
                    if ($componentinfo->showOnDuplicate) {
                        $rowkey = $_GET["duplicate"];
                        $fieldValue = $componentinfo->GetDisplayDuplicateHtml($rawvalue, $key, $hvalue, $this->model, $rowkey);
                    } else continue;
                } else if ($this->editmode) {
                    if ($componentinfo->showOnEdit) {
                        $rowkey = $_GET["edit"];
                        $fieldValue = $componentinfo->GetDisplayEditHtml($rawvalue, $key, $hvalue, $this->model, $rowkey);
                    } else continue;
                } else {

                    if ($this->addMode) {
                        if ($componentinfo->showOnAdd) {
                            $fieldValue = $componentinfo->GetDisplayAddHtml($rawvalue, $key, $hvalue, $this->model);
                        } else continue;
                    } else {
                        continue;
                    }

                }


                $page = 1;

                $xtraFormClassWrapper = $hvalue->xtraFormClassWrapper . " ";
                if ($this->editmode) {
                    if ($hvalue->editpage >= 0) {
                        $page = $hvalue->editpage;
                    }
                    $xtraFormClassWrapper .= "page-edit";
                }

                if ($this->addMode || $this->duplicatemode) {
                    if ($hvalue->addpage >= 0) {
                        $page = $hvalue->addpage;
                    }
                    $xtraFormClassWrapper .= "page-add";
                }

                if ($this->duplicatemode) {
                    if ($hvalue->addpage >= 0) {
                        $page = $hvalue->addpage;
                    }
                    $xtraFormClassWrapper .= "page-add page-duplicate";
                }

                $ishidden = false;
                if ($hvalue->display == "hidden" || $hvalue->hiddenOnEdit && $this->editmode || $hvalue->hiddenOnAdd && $this->addMode) {
                    $ishidden = true;
                }

                if (!empty($fieldValue)) {
                    echo "<div ";

                    if ($ishidden) {
                        echo "style='visibility:hidden;position:absolute;'";
                    }

                    echo " class='component component-" . $hvalue->display . " $xtraFormClassWrapper page-$page field_" . $hvalue->name . "' >";


                    if ($hvalue->showLabelInForm) {
                        echo "<span class = 'admin_form_caption admin_form_caption_" . $hvalue->display . "' >" . str_replace("<br>", "", $hvalue->label) . (!empty($hvalue->label) && $hvalue->show_colon ? ":" : "") . "</span>";
                    }

                    echo '<span class="component_wrapper component_wrapper_' . $hvalue->display . '">';
                    /*if(is_array($fieldValue)){
                        var_dump($fieldValue);
                    }else{
                        echo $fieldValue;
                    }*/
                    echo $fieldValue;
                    echo '</span></div>';
                }

            }

            echo "</div>";

        }


        // if ($position != "right") {
        ?>
        <div class='buttonbar-afterform-wrap'>
            <?php
            $nextbuttoncss = "display:none;";
            $prevbuttoncss = "display:none;";
            if (!$this->isLastPage && !$this->editmode) {
                $nextbuttoncss = "";
            }
            ?>
            <div class="buttonbar-afterform">


                <a style='<?= $prevbuttoncss ?>' class="directionButton btn btn-default prevButton bpfwbutton button">

                    <div class="new-entry-button"><i class="topbuttonicon fas fa-chevron-left"></i>
                        <div>zurück</div>

                    </div>

                </a>

                <a href="<?php echo BASE_URI; ?>index.php?p=<?php echo $_GET["p"] . "&filter=" . getorpost("filter"); ?>"
                   class="btn btn-default cancelAction cancelButton bpfwbutton button <?php echo $this->model->showCancelButton ? "" : "hiddenButton hiddenCancel"; ?>">

                    <div class="new-entry-button">
                        <i class="topbuttonicon fas fa-times"></i>
                        <div><?php echo __("Close"); ?></div>

                    </div>

                </a>


                <a style='<?= $nextbuttoncss ?>'
                   class="directionButton btn btn-default continueButton bpfwbutton  button">

                    <div class="new-entry-button"><i class="topbuttonicon fa fa-chevron-right"></i>
                        <div>weiter</div>
                    </div>

                </a>


                <?php
                //echo '<button type="button" style='.$nextbuttoncss.' class="directionButton btn btn-default continueButton">weiter</button>';
                //echo '<button type="button" style='.$prevbuttoncss.' class="directionButton btn btn-default prevButton">zurück</button>';
                ?>
                <!--   <a href="<?php echo BASE_URI; ?>index.php?p=<?php echo $_GET["p"] . "&filter=" . getorpost("filter"); ?>">
                        <button type="button" style="color:black;margin-top:10px;" class="btn btn-default cancelAction cancelButton" name="cancelAction">Abbrechen</button>
                    </a> -->

                <?php


                $buttoncss = "";
                if (!$this->isLastPage) {
                    $buttoncss = "display:none";
                }

                $confirmbuttontxt = __("Add");
                $confirmaction = "addAction";
                $faicon = "fa-plus";

                if (!isset ($_GET ['duplicate']) && isset ($_GET ['edit'])) {
                    $confirmbuttontxt = __("Save");
                    $confirmaction = "editAction";
                    $faicon = "fa-edit";
                }

                ?>

                <?php if ($this->model->showSubmitButton) { ?>
                    <button type="submit" name="<?= $confirmaction; ?>" style="<?= $buttoncss; ?>"
                            class="btn btn-default bpfwbutton button <?= $confirmaction; ?>">

                        <div class="new-entry-button"><i class="topbuttonicon fas <?= $faicon; ?>"></i>
                            <div><?= $confirmbuttontxt; ?></div>

                        </div>

                    </button>
                <?php } ?>
                <?php

                /*  echo '<input style="'.$buttoncss.'" type="submit" class="btn btn-default editAction" name="editAction" value="Übernehmen">';
                } else {
                echo '<input style="'.$buttoncss.'" type="submit" class=" btn btn-default addAction" name="addAction" value="Hinzufügen">';
                }*/

                ?>


            </div>

        </div>
        <?php
        // }


        echo "</form>";

    }

    /**
     * @throws Exception
     */
    function renderTable()
    {

        $maintablecss = "";

        if ($this->addMode) {

            $maintablecss = "display:none;";
        } else
            if (!empty($_POST) && !bpfw_hasPermission($this->model->minUserrankForShow)) {

                echo "<br>";

                echo "Eintrag erstellt";

                return;

            }


        ?>

        <div id="listcontent_before" class="hasAjaxTable defaultlistmodalview"></div>

        <div style="<?php echo $maintablecss; ?>" id="listcontent" class="hasNoAjaxTable defaultlistmodalview">

            <?php bpfw_do_action(DefaultlistView::ACTION_DEFAULTLISTVIEW_BEFORE_RENDER_TABLE, $this->model->GetTableName()); ?>

            <?php if ($this->model->showTable) { ?>

                <table data-defaultsort="<?php echo $this->model->sortColumn; ?>"
                       data-defaultsortorder="<?php echo $this->model->sortOrder; ?>" width="100%" id="adminTable"
                       class="display admintable">

                    <thead>

                    <tr role="row">

                        <?php
                        foreach ($this->model->getFieldsForList() as $hkey => $hvalue) {

                            $componentinfo = bpfw_getComponentHandler()->getComponent($hvalue->display);
                            /*

                        if($this->print && $hvalue->hiddenOnPrint)continue;
                        if($hvalue->hiddenOnList)continue;
                        if(!empty($hvalue->parentComponent))continue;


                        if(!$componentinfo->showOnList){
                            continue;
                        }*/

                            $cssClasses = "";
                            if ($hvalue->ajax_sortable === false || $hvalue->ajax_sortable === null && !$componentinfo->defaultSortable) {
                                $cssClasses = "no-sort";
                            }

                            $label = $hvalue->label;
                            if (isset($hvalue->label_list) && $hvalue->label_list != "like_form_label") {
                                $label = $hvalue->label_list;
                            }
                            echo "<th class='$cssClasses' data-name='$hkey' style='white-space:nowrap' >" . $label . "</th>";

                        }
                        ?>

                        <?php if ($this->hasAnyButtons) { ?>
                            <th data-name='bpfw_datalist_buttons' style="width:20px;"></th>
                        <?php } ?>
                    </tr>

                    </thead>

                    <tbody>
                    <?php

                    if ($this->getEntries() != null) {

                        // alle Einträge als Objekte
                        foreach ($this->getEntries() as $key => $value) {


                            if (!bpfw_do_check(DefaultlistView::ACTION_DEFAULTLISTVIEW_DISPLAY_ENTRY, $this->model->getTablename(), array($value))) continue;

                            echo '<tr>';

                            foreach ($this->model->getFieldsForList() as $hkey => $hvalue) {

                                $componentinfo = bpfw_getComponentHandler()->getComponent($hvalue->display);

                                /*if($hvalue->hiddenOnList == true)continue;

                                if(!empty($hvalue->parentComponent))continue;


                                if(!$componentinfo->showOnList){
                                    continue;
                                }*/

                                // if($hvalue->name == "file")echo "ok";

                                echo "<td ";
                                $sortValue = $componentinfo->getSortValue($value->$hkey, $hkey, $hvalue, $key, $this->model);

                                if (!empty($sortValue)) {
                                    echo " data-order='$sortValue' ";
                                }

                                echo $componentinfo->getDataHtml($hvalue->data);

                                echo '>';

                                // echo " data-hkey='$hkey' data-key='$key' data-value='$value'>";

                                echo $componentinfo->GetDisplayLabelHtml($value->$hkey, $hkey, $hvalue, $key, $this->model);

                                echo "</td>";

                            }

                            if ($this->hasAnyButtons) {

                                echo "<td class='list-button-tablefield-nonmodal' style='white-space:nowrap'>";

                                echo $this->getButtonIconHtml($key, $value);

                                echo '</td>';

                            }

                            echo '
 		</tr>';

                        }
                    }

                    ?>

                    </tbody>
                </table>

            <?php } ?>

        </div>

        <?php

    }

    /**
     * @throws Exception
     */
    function getEntries(): array
    {

        // is cached, so no need for saving them
        if (bpfw_hasPermission($this->model->minUserrankForShow)) {
            return $this->model->GetEntries();
        }
        return array();
    }

    /**
     * @throws Exception
     */
    function getButtonIconHtml($rowkey, $rowvalues = "", $model = null, $view = null, $temptable = false): bool|string
    {

        ob_start();

        echo "<span class='datalist_button_wrapper'>";

        if (bpfw_countEditableFields($this->model->getDbModel()) > 0) {

            if ($this->makeEdit)
                echo '
		                    <a data-id=' . $rowkey . ' title="Editieren" href="?p=' . $this->model->getSlug() . '&edit=' . $rowkey . '&filter=' . $this->filter . '" class="edit-button tableicon_size" href="#"><i class="tableicon fa fa-edit">' . __("Edit") . '</i></a>
                            ';

            if ($this->makeDuplicate)
                echo '
		                    <a data-id=' . $rowkey . ' title="Duplizieren" href="?p=' . $this->model->getSlug() . '&duplicate=' . $rowkey . '&filter=' . $this->filter . '" class="duplicate-button tableicon_size" href="#"><i class="tableicon fa fa-clone">' . __("Duplicate") . '</i></a>
                            ';

        }

        if ($this->makeTrash) {
            echo '<a data-id=' . $rowkey . ' title="Löschen" class="delete-button tableicon_size" '; ?>
            onClick="return deleteEntry(<?php echo $rowkey; ?>);"<?php
            echo ' href="?p=' . $this->model->getSlug() . '&delete=' . $rowkey . '&filter=' . $this->filter . '"><i class="tableicon fa fa-trash-alt">' . __("Delete") . '</i></a>';
        }

        bpfw_do_action(DefaultlistView::ACTION_DEFAULTLISTVIEW_DISPLAY_BUTTONS, $this->model->GetTableName(), array($rowkey, $rowvalues), $this);

        if ($this->makeSendPassword) {

            $icon = "fa-unlock";
            if (empty($rowvalues->password)) $icon = "fa-lock";

            echo '<a title="Passwort zurücksetzen" class="tableicon_size" '; ?>
            onClick="return confirm('Möchten Sie dem User wirklich ein neues zufällig generiertes Passwort zuschicken?')"<?php
            echo ' href="?p=' . $this->model->getSlug() . '&resetPassword=' . $rowkey . '&filter=' . $this->filter . '"><i class="tableicon fa ' . $icon . '">' . __("Send Password") . '</i></a>';

        }

        return ob_get_clean();
    }

    function registerJs(): void
    {

        bpfw_register_js("defaultlistview_js", BPFW_JS_URI . "defaultlistView_v2.js", true);

    }

    /**
     * @throws Exception
     */
    function initializeVariables()
    {

        // $this->entry = null;

        // var_dump($components);

        if (!bpfw_error_hasErrors() && isset($_POST['addAction'])) {
            unset($_GET["openAdd"]);
        }

        $usertype = bpfw_getCurrentUsertype();

        $this->editmode = false;
        $editEntry = null;

        $this->duplicatemode = false;
        $duplicateEntry = null;

        if (isset ($_GET ['edit'])) {

            //echo "edit";
            //echo getorpost("temptable");

            $editEntry = $this->model->GetEntry($_GET['edit'], 'default', -1, 0, '', !empty(getorpost("temptable")) && getorpost("temptable") != "false" && getorpost("temptable") != "0");
            // echo $_GET['edit'];

            if (!empty($editEntry)) $this->editmode = true;


        } else {
            if (isset ($_GET ['duplicate'])) {
                $duplicateEntry = $this->model->GetEntry($_GET['duplicate'], 'default', -1, 0, '', !empty(getorpost("temptable")) && getorpost("temptable") != "false" && getorpost("temptable") != "0");
                if (!empty($duplicateEntry)) $this->duplicatemode = true;
            }
        }


//        isset ( $_GET ['edit'] ) &&  $this->model->GetEntry( $_GET['edit'],' 1', -1,0,'', getorpost("temptable")) != null;

//        $this->duplicatemode = isset ( $_GET ['duplicate'] )  &&  $this->model->GetEntry($_GET['duplicate'],' 1', -1,0,'', getorpost("temptable") ) != null;
        $this->addMode = isset ($_GET ['openAdd']);

        $this->currentPage = 1;
        $this->lastPage = 1;

        $this->lastPage = $this->model->getMaxPage($this->editmode);
        $this->isLastPage = $this->lastPage == $this->currentPage;
        $this->hasMultiplePages = $this->model->hasMultiplePages();


        if ($this->editmode) {
            //$this->islastPage = $this->currentpage == $this->model->pagesEdit;
            //$this->lastpage = $this->model->pagesEdit;
            //echo "edit";
            $this->entry = $editEntry;
        } else if ($this->duplicatemode) {
            $this->entry = $duplicateEntry;
        } else {
            // $this->islastPage = $this->currentpage == $this->model->pagesAdd;
            // $this->lastpage = $this->model->pagesAdd;
        }


        $this->print = false;

        if (isset($_GET["showPrint"]) && $_GET["showPrint"]) {
            $this->print = true;
            $this->hideNavigation = true;
        }

        if (isset($_GET["hideNavigation"]) && $_GET["hideNavigation"]) {
            $this->hideNavigation = true;
        }

        $this->filter = "";
        if (!empty(getorpost("filter"))) {
            $this->filter = getorpost("filter");
        }

        // bottom List /////////////////////////////////////////////////////////////////////

        $this->makeTrash = !$this->print && bpfw_hasPermission($this->model->minUserrankForDelete); //bpfw_isAdmin();

        $this->makeEdit = !$this->print && bpfw_hasPermission($this->model->minUserrankForEdit); //true;

        $this->makeDuplicate = !$this->print && bpfw_hasPermission($this->model->minUserrankForDuplicate); //true;


        $this->makeSendPassword = !$this->print && $this->model->getSlug() == "user" && bpfw_isAdmin();


        $this->hasAnyButtons = $this->hasAnyButtons || $this->makeEdit || $this->makeTrash || $this->makeDuplicate || $this->makeSendPassword;
        // bpfw_register_js("jquery_validation", LIBS_URI."jquery-validation-1.19.1/dist/jquery.validate.min.js", true);


    }

    /**
     * @throws Exception
     */
    function getAddTitle(): string
    {
        return __("Create entry");
    }

    /**
     * @throws Exception
     */
    function getEditTitle(int $id): string
    {

        if (method_exists($this->model, "getEditTitle")) {
            return $this->model->getEditTitle($id);
        }

        return __("Edit entry") . ": " . $this->getIdentifierForDialogTitle($id);
    }

    function getIdentifierForDialogTitle($id)
    {
        return $id;
    }

    /**
     * @throws Exception
     */
    function getDuplicateTitle(int $id): string
    {
        return __("Duplicate entry") . ": " . $this->getIdentifierForDialogTitle($id);
    }

    /**
     * @throws Exception
     */
    function getDeleteTitle(int $id): string
    {
        return __("Delete entry") . ": " . $this->getIdentifierForDialogTitle($id);
    }

}