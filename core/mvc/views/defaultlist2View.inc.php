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

require_once(BPFW_MVC_PATH . "views/defaultlistView.inc.php");

class DefaultList2View extends DefaultlistView
{


    function createIFrameDialog($identifier, $hierachy = 0, $link = "", $dialogsize = "modal-lg modal-xlg"): bool|string
    {

        ob_start();

        $xtracss = "";

        if ($hierachy != 0) {
            $zindex = 1050 + $hierachy * 2;
            $xtracss = "z-index:$zindex";
        }

        ?>

        <div data-shownonmodal="1" data-model="<?php echo $this->model->GetTableName() ?>"
             data-hierachy="<?php echo $hierachy ?>" style="<?php echo $xtracss; ?>"
             class="dialogmodel_<?php echo $this->model->GetTableName() ?> shownonmodal manipulateDataDialog  editEntryDialog layer_<?php echo $hierachy ?>"
             id="<?php echo $identifier ?>" tabindex="-1" role="dialog" aria-labelledby="ModalCenterTitle"
             aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered <?php echo $dialogsize; ?>" role="document">

                <div class="modal-content">

                    <div class="modal-body" style="margin:auto; padding:20px;">


                        <div class="h_contentframe">
                            <iframe class="contentframe" frameborder="0" allowfullscreen=""
                                    src="<?php echo $link; ?>"></iframe>
                        </div>


                    </div>

                    <!-- enable if higher hierachy dialog is enabled -->
                    <div data-hierachy="<?php echo $hierachy ?>" class="dialog_disabled_overlay"
                         style="position:absolute;top:0;left:0;width:100%;height:100%;background-color:rgba(0,0,0,0.3);z-index:1;display:none;"></div>

                </div>

            </div>

        </div>


        <?php

        return ob_get_clean();

    }


    /**
     * @throws Exception
     */
    function createEditDialog($identifier, $hierachy = 0, $rowid = -1, $dialogsize = "modal-lg modal-xlg", $temptable = false): bool|string
    {

        ob_start();

        $xtracss = "";

        if ($hierachy != 0) {
            $zindex = 1050 + $hierachy * 2;
            $xtracss = "z-index:$zindex";
        }

        ?>

        <div data-temptable="<?php echo (int)$temptable; ?>" data-shownonmodal="1" data-rowid="<?php echo $rowid; ?>"
             data-model="<?php echo $this->model->GetTableName() ?>" data-hierachy="<?php echo $hierachy ?>"
             style="<?php echo $xtracss; ?>"
             class="dialogmodel_<?php echo $this->model->GetTableName() ?>  shownonmodal manipulateDataDialog  editEntryDialog layer_<?php echo $hierachy ?>"
             id="<?php echo $identifier ?>" tabindex="-1" role="dialog" aria-labelledby="ModalCenterTitle"
             aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered <?php echo $dialogsize; ?>" role="document">

                <div class="modal-content">

                    <div class="modal-body" style="margin:auto; padding:20px;">

                        <?php

                        echo '<div class="errorMessages">';

                        foreach (bpfw_error_get() as $error) {
                            echo $error->msg;
                            echo "<br>";
                        }

                        echo '</div>';


                        $this->renderTabs(true, $identifier);
                        ?>

                        <div class="dialog-form-content">
                            <i style="font-size:70px;margin:auto;" class="fas fa-spinner fa-spin"></i>
                        </div>
                    </div>
                    <div class="modal-footer defaultlistmodalview shownonmodalbuttonbar">

                        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                                        <button type="button" class="btn btn-primary">Speichern</button> -->

                        <?php
                        $this->renderButtonBar(true);
                        ?>

                    </div>

                    <!-- enable if higher hierachy dialog is enabled -->
                    <div data-hierachy="<?php echo $hierachy ?>" class="dialog_disabled_overlay"
                         style="position:absolute;top:0;left:0;width:100%;height:100%;background-color:rgba(0,0,0,0.3);z-index:1;display:none;"></div>

                </div>

            </div>

        </div>


        <?php

        return ob_get_clean();

    }

    /**
     * @throws Exception
     */
    function renderTabs($editmode = false, $parentformidentifier = "")
    {

        if ($this->model->showTabs) {

            $tabnavigationname = $editmode ? "editmodeTabNavigation" : "addmodeTabNavigation";
            ?>

        <ul class="nav nav-tabs nav-pills dialogTabNavigation<?php if (!$this->model->hasMultiplePages($editmode)) {
            echo "singlePage";
        } ?>" id="<?php echo $tabnavigationname; ?>" role="tablist">
            <?php

            if ($this->model->hasMultiplePages($editmode)) {

                $first = true;

                for ($i = 1; $i <= $this->model->getMaxPage($editmode); $i++) {

                    $tabname = $this->model->getTabName($editmode, $i);

                    $selected = 0;

                    if ($first) {
                        $selected = 1;
                    }

                    $tabid_class = " tab-" . ($editmode ? "edit" : "add") . "-" . $i;
                    $tabcontentid = "tab-" . ($editmode ? "edit" : "add") . "-" . $i . "-content";

                    $tabid = "tab-" . ($this->editmode ? "edit" : "add") . "-" . $i;

                    //$tabcountclass = "tab-".($this->editmode?"edit":"add")."-".$i."-content";

                    $tabcontentid = "tab-$parentformidentifier-$i-content";
                    $tabid = "tab-$parentformidentifier-$i";


                    $toggle = "tab";
                    if (in_array($i, $this->model->tabsWithoutTabFunction)) {
                        $toggle = "";
                    }

                    ?>

                    <li class="<?php echo $this->model->getTabClasses($editmode, $i); ?> nav-item tab-<?php echo $i ?>">
                        <a data-page="<?php echo $i; ?>"
                           class="<?php echo $tabid_class; ?> nav-link <?php if ($selected) echo "active"; ?> "
                           id="<?php echo $tabid ?>" data-toggle="<?php echo $toggle; ?>"
                           href="#<?php echo $tabcontentid; ?>" role="tab" aria-controls="<?php echo $tabcontentid; ?>"
                           aria-selected="<?php echo $selected; ?>">
                            <?php echo $tabname; ?>
                        </a>
                    </li>


                    <?php

                    $first = false;

                }

                ?>
                </ul>
                <?php

            }
        }

    }

    /**
     * @throws Exception
     */
    function renderButtonBar($editmode = false)
    {
        ?>

        <div class='buttonbar-afterform-wrap'>

            <?php
            $filter = "";
            if (!empty(getorpost("filter"))) {
                $filter = getorpost("filter");
            }

            $page = "";
            if (!empty(getorpost("p"))) {
                $page = getorpost("p");
            }


            $nextbuttoncss = "display:none;";
            $prevbuttoncss = "display:none;";
            if (!$this->isLastPage && (!$editmode && $this->model->showPrevNextButtonsOnAddDialog || $editmode && $this->model->showPrevNextButtonsOnEditDialog)) {
                $nextbuttoncss = "";
            }


            $buttoncss = "";
            if (!$this->isLastPage && (!$editmode && $this->model->showPrevNextButtonsOnAddDialog || $editmode && $this->model->showPrevNextButtonsOnEditDialog)) {
                $buttoncss = "display:none";
            }

            $confirmbuttontxt = __("Add");
            $confirmaction = "addAction";
            $faicon = "fa-plus";

            if ($editmode) {
                $confirmbuttontxt = __("Save");
                $confirmaction = "editAction";
                $faicon = "fa-edit";
            }


            ?>
            <div class="buttonbar-afterform" data-model="<?php echo $this->model->GetTableName() ?>">


                <?php
                if ($this->model->showPrevNextButtonsOnAddDialog && !$editmode || $editmode && $this->model->showPrevNextButtonsOnEditDialog) {
                    ?>

                    <a style='<?= $prevbuttoncss ?>'
                       class="directionButton btn btn-default prevButton bpfwbutton button">

                        <div class="new-entry-button"><i class="topbuttonicon fas fa-chevron-left"></i>
                            <div>zurück</div>

                        </div>

                    </a>
                <?php } ?>


                <?php if ($this->model->showSubmitButton) { ?>
                    <button type="submit" name="<?= $confirmaction; ?>" style="<?= $buttoncss; ?>"
                            class="btn btn-primary btn-default bpfwbutton button <?= $confirmaction; ?>">

                        <div class="new-entry-button">
                            <i class="topbuttonicon fas <?= $faicon; ?>"></i>
                            <div>
                                <?= $confirmbuttontxt; ?>
                            </div>

                        </div>

                    </button>
                <?php } ?>


                <a href="<?php echo BASE_URI; ?>index.php?p=<?php echo $page . "&filter=" . $filter; ?>"
                   data-dismiss="modal"
                   class="btn btn-secondary btn-default cancelAction cancelButton bpfwbutton button <?php echo $this->model->showCancelButton ? "" : "hiddenButton hiddenCancel"; ?>">

                    <div class="new-entry-button">
                        <i class="topbuttonicon fas fa-times"></i>
                        <div><?php echo __("Close"); ?></div>
                    </div>

                </a>

                <?php
                if ($this->model->showPrevNextButtonsOnAddDialog && !$editmode || $editmode && $this->model->showPrevNextButtonsOnEditDialog) {
                    ?>
                    <a onClick="" style='<?= $nextbuttoncss ?>'
                       class="directionButton btn btn-default continueButton bpfwbutton  button">

                        <div class="new-entry-button"><i class="topbuttonicon fa fa-chevron-right"></i>
                            <div>weiter</div>
                        </div>

                    </a>
                <?php }


                /*  echo '<input style="'.$buttoncss.'" type="submit" class="btn btn-default editAction" name="editAction" value="Übernehmen">';
                } else {
                echo '<input style="'.$buttoncss.'" type="submit" class=" btn btn-default addAction" name="addAction" value="Hinzufügen">';
                }*/


                ?>

            </div>

        </div>
        <?php
    }

    /**
     * @throws Exception
     */
    function createAddDialog($identifier, $hierachy = 0, $rowid = -1, $dialogsize = "modal-lg modal-xlg", $temptable = false): bool|string
    {

        ob_start();

        $xtracss = "";

        if ($hierachy != 0) {
            $zindex = 1050 + $hierachy * 2;
            $xtracss = "z-index:$zindex";
        }


        ?>


        <div data-temptable="<?php echo (int)$temptable; ?>" data-shownonmodal="1" data-rowid="<?php echo $rowid; ?>"
             data-model="<?php echo $this->model->GetTableName() ?>" style="<?php echo $xtracss; ?>"
             data-hierachy="<?php echo $hierachy ?>"
             class="dialogmodel_<?php echo $this->model->GetTableName() ?>  shownonmodal manipulateDataDialog  addEntryDialog layer_<?php echo $hierachy; ?>"
             id="<?php echo $identifier; ?>" tabindex="-1" role="dialog" aria-labelledby="ModalCenterTitle"
             aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered <?php echo $dialogsize; ?>" role="document">

                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="ModalLongTitle"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body" style="margin:auto; padding:20px;">

                        <?php

                        echo '<div class="errorMessages">';

                        foreach (bpfw_error_get() as $error) {
                            echo $error->msg;
                            echo "<br>";
                        }

                        echo '</div>';


                        $this->renderTabs(false, $identifier);
                        ?>

                        <div class="dialog-form-content">
                            <i style="font-size:70px;margin:auto;" class="fas fa-spinner fa-spin"></i>
                        </div>

                    </div>

                    <div class="modal-footer defaultlistmodalview shownonmodalbuttonbar">

                        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                            <button type="button" class="btn btn-primary">Speichern</button> -->

                        <?php
                        $this->renderButtonBar();
                        ?>

                    </div>

                    <!-- enable if higher hierachy dialog is enabled -->
                    <div data-hierachy="<?php echo $hierachy ?>" class="dialog_disabled_overlay"
                         style="position:absolute;top:0;left:0;width:100%;height:100%;background-color:rgba(0,0,0,0.3);z-index:1;display:none;"></div>

                </div>

            </div>

        </div>

        <?php

        return ob_get_clean();

    }

    function renderForm()
    {

        // $dialogsize = "modal-lg";

        foreach ($this->model->getDbModel() as $key => $hvalue) {
            if ($hvalue->position == POSITION_RIGHT) {
                $dialogsize = "modal-lg modal-xlg";
            }
        }

        ?>

        <div id="dialogWrapper" data-current-hierachy="-1">
            <?php

            // $this->createEditDialog($dialogsize,0);

            // $this->createAddDialog($dialogsize,0);

            ?>
        </div>
        <?php


    }

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

        <div style="<?php echo $maintablecss; ?>" id="listcontent" class="hasAjaxTable defaultlist2view">

            <?php bpfw_do_action(DefaultlistView::ACTION_DEFAULTLISTVIEW_BEFORE_RENDER_TABLE, $this->model->GetTableName()); ?>





            <?php if ($this->model->showTable) { ?>

                <table data-defaultsort="<?php echo $this->model->sortColumn; ?>"
                       data-defaultsortorder="<?php echo $this->model->sortOrder; ?>" width="100%" id="adminTable_ajax"
                       class="display admintable">

                    <thead>

                    <tr role="row">

                        <?php if ($this->hasAnyButtons) { ?>

                            <th data-name='bpfw_datalist_empty' class="no-sort"></th>

                            <th data-name='bpfw_datalist_buttons' style="width:20px;"></th>

                        <?php } ?>

                        <?php
                        foreach ($this->model->getFieldsForList() as $hkey => $hvalue) {

                            $componentinfo = bpfw_getComponentHandler()->getComponent($hvalue->display);


                            /*if($this->print && $hvalue->hiddenOnPrint)continue;
                            if($hvalue->hiddenOnList)continue;
                            if(!empty($hvalue->parentComponent))continue;


                            if(!$componentinfo->showOnList){continue;}*/

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


                    </tr>

                    </thead>

                    <tbody>
                    </tbody>

                </table>

            <?php } ?>

        </div>

        <?php

    }


    function registerJs(): void
    {


        bpfw_register_js("defaultlistmodalview_js", BPFW_JS_URI . "defaultlistmodalView.js?v=13", true);

        parent::registerJs();

    }

    /**
     * @throws Exception
     */
    function renderAddModalContent($parentformidentifier)
    {

        $this->renderFormContent($parentformidentifier);

    }

    /**
     * @throws Exception
     */
    private function renderFormContent($parentformidentifier)
    {

        echo '<div class="modal-content-wrapper">';


        if (!$this->editmode) {

            echo '<form autocomplete="off"  data-lastpage="' . $this->lastPage . '" id="listviewForm" enctype="multipart/form-data" class="edit-content-form defaultlistmodalview"  action="" method="post" id="addNewElement"';
            if ((!isset ($_GET ['openAdd']) || $_GET ['openAdd'] != "true") &&
                (!isset ($_GET ['duplicate']) || !is_numeric($_GET ['duplicate']))) {
                echo 'style="display:none;"'; // none
            } else {
                echo 'style="display:block;"';
            }
            echo ' >';

        } else {

            echo '<form autocomplete="off"  data-lastpage="' . $this->lastPage . '" id="listviewForm" enctype="multipart/form-data" action="" class="edit-content-form defaultlistmodalview" method="post" id="addNewElement" style="display:block;" >';

        }


        $tabname = $this->editmode ? "editmodeTabNavigationContent" : "addmodeTabNavigationContent";

        echo '<div class="tab-content dialogTabNavigationContent '; ?>
        <?php if (!$this->model->hasMultiplePages($this->editmode)) {
        echo "singlePage";
    } ?>
        <?php echo '" id="' . $tabname . '">';

        $first = true;

        for ($page = 1; $page <= $this->model->getMaxPage($this->editmode); $page++) {

            $tabid = "tab-" . ($this->editmode ? "edit" : "add") . "-" . $page;

            $tabcountclass = "tab-" . ($this->editmode ? "edit" : "add") . "-" . $page . "-content";
            $tabcontentid = "tab-$parentformidentifier-$page-content";
            $tabid = "tab-$parentformidentifier-$page";


            $active = "";
            if ($first)
                $active = " show active ";

            echo '<div class="' . $tabcountclass . ' tab-pane fade ' . $active . '" id="' . $tabcontentid . '" role="tabpanel" aria-labelledby="' . $tabid . '">';

            $positions = $this->model->getPositions();

            foreach ($positions as $position) {

                echo "<div class='form_position_" . $position . "' >";

                foreach ($this->model->getDbModel() as $key => $hvalue) {

                    /*   echo $hvalue->editpage;
                       echo " == ";
                       echo $page;
                       echo " "; */

                    if ($page != $hvalue->editpage && $this->editmode) continue;

                    if ($page != $hvalue->addpage && ($this->addMode || $this->duplicatemode)) continue;

                    /* if(isset($this->duplicatemode) ){
                         echo getorpost("duplicate");
                       if($page != getorpost("duplicate"))continue;
                     }*/

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

                    if ($this->editmode && $hiddenedit) {
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


                    //  $page_nontab = $page;

                    $xtraFormClassWrapper = $hvalue->xtraFormClassWrapper . " ";
                    if ($this->editmode) {
                        if ($hvalue->editpage >= 0) {
                            //   $page_nontab = $hvalue->editpage;
                        }
                        $xtraFormClassWrapper .= "page-edit";
                    }

                    if ($this->addMode || $this->duplicatemode) {
                        if ($hvalue->addpage >= 0) {
                            //    $page_nontab = $hvalue->addpage;
                        }
                        $xtraFormClassWrapper .= "page-add";
                    }

                    if ($this->duplicatemode) {
                        if ($hvalue->addpage >= 0) {
                            //   $page_nontab = $hvalue->addpage;
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


                        echo " class='component component-" . $hvalue->display . " $xtraFormClassWrapper modal-page-$page field_" . $hvalue->name . "' >";


                        if ($hvalue->showLabelInForm)
                            echo "<span  class = 'admin_form_caption admin_form_caption_" . $hvalue->display . "' >" . str_replace("<br>", "", $hvalue->label) . (!empty($hvalue->label) && $hvalue->show_colon ? ":" : "") . "</span>";

                        echo '<span class="component_wrapper component_wrapper_' . $hvalue->display . '" >';
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


            $first = false;

            echo "</div>";

        }
        echo "</div>";

        echo "<button data-model='" . $this->model->GetTableName() . "' type='submit' style='display:none' id='hiddensubmit' data-css_id='" . $parentformidentifier . "'>";

        echo "</form>";

        echo '</div>';

    }

    /**
     * @throws Exception
     */
    function renderEditModalContent($id, $parentformidentifier)
    {

        $this->renderFormContent($parentformidentifier);

    }

    function getButtonIconHtml($rowkey, $rowvalues = "", $model = null, $view = null, $temptable = false): bool|string
    {

        if ($model == null) {
            $model = $this->model;
        }

        if (empty($view)) {
            if (empty($model->view)) {
                $view = $this;
            } else {
                $view = $model->view;
            }
        }


        ob_start();


        $isRootHierachy = "true";
        if (!$this->model->isRootHierachy) {
            $isRootHierachy = "false";
        }

        echo "<span class='datalist_button_wrapper'>";

        if (bpfw_countEditableFields($view->model->getDbModel()) > 0) {

            if ($view->makeEdit)
                echo '
		                    <a data-hierachy="0" data-model="' . $model->getSlug() . '" data-id=' . $rowkey . ' title="Editieren" onclick="return startEdit(\'' . $rowkey . '\', \'' . trim($model->getSlug()) . '\', ' . $isRootHierachy . ', ' . ($temptable ? "true" : "false") . ')" class="root_button edit-button tableicon_size" href="#"><i class="tableicon fa fa-edit">' . __("Edit") . '</i></a>
                            ';

            if ($view->makeDuplicate)
                echo '
		                    <a data-model="' . $model->getSlug() . '" data-id=' . $rowkey . ' title="Duplizieren" onclick="return startDuplicate(\'' . $rowkey . '\', \'' . trim($model->getSlug()) . '\', ' . $isRootHierachy . ', ' . ($temptable ? "true" : "false") . ')" class="root_button duplicate-button tableicon_size" href="#"><i class="tableicon fa fa-clone">' . __("Duplicate") . '</i></a>
                            ';

        }

        if ($view->makeTrash) {
            echo '<a data-model="' . $model->getSlug() . '" data-id=' . $rowkey . ' title="Löschen" class="delete-button tableicon_size" '; ?>
            onClick="return deleteEntry('<?php echo $rowkey; ?>', '<?php echo $model->getSlug(); ?>', <?php echo (int)$temptable ?> );"<?php
            echo ' href="?p=' . $model->getSlug() . '&delete=' . $rowkey . '&filter=' . $view->filter . '"><i class="tableicon fa fa-trash-alt">' . __("Delete") . '</i></a>';
        }

        bpfw_do_action(DefaultlistView::ACTION_DEFAULTLISTVIEW_DISPLAY_BUTTONS, $model->GetTableName(), array($rowkey, $rowvalues), $view);

        if ($this->makeSendPassword) {

            $icon = "fa-unlock";
            if (empty($rowvalues->password)) $icon = "fa-lock";

            echo '<a data-model="' . $model->getSlug() . '" title="Passwort zurücksetzen" class="tableicon_size" '; ?>
            onClick="return confirm('Möchten Sie dem User wirklich ein neues zufällig generiertes Passwort zuschicken?')"<?php
            echo ' href="?p=' . $model->getSlug() . '&resetPassword=' . $rowkey . '&filter=' . $view->filter . '"><i class="tableicon fa ' . $icon . '"></i></a>';

        }

        return ob_get_clean();

    }

}