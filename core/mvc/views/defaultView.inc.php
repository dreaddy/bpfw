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
class DefaultView
{


    /**
     * Summary of $model
     * @var BpfwModel|null
     */
    var BpfwModel|null $model = null;

    /**
     * Summary of __construct
     * @param BpfwModel $model
     * @throws Exception
     * @throws Exception
     */
    function __construct(BpfwModel $model)
    {

        //if($model == null)throw new Exception("model is null");

        $this->model = $model;
        $this->model->setView($this);


        bpfw_register_js("defaultlistmodalview_js", BPFW_JS_URI . "defaultlistmodalView.js?v=13", true);

        // view js
        $name = get_class($this); // original name
        $this->tryIncludeJs($name);
        $this->tryIncludeCss($name);

        // model js
        $name = get_class($this->model);

        if (ctype_alnum($name)){
            $this->tryIncludeJs($name);
            $this->tryIncludeCss($name);
        }

        $this->addChildJs($this->model->getDbModel(), array());


    }

    function tryIncludeJs($name): void
    {

        if (empty($name) || !is_string($name) || !ctype_alnum($name)) return;

        $nameToCheck = $name;

        $appfound = false;
        $parentfound = false;
        $bpfwfound = false;
        //var_dump($name);
        //echo APP_WWW_PATH." /js ".$name." .js";
        if (file_exists(APP_WWW_PATH . "js/" . $name . ".js")) {
            $appfound = true;
            bpfw_register_js($name . "_js", APP_WWW_URI . "js/" . $name . ".js?v=10", true);
        }

        if (PARENT_WWW_PATH != APP_WWW_PATH && file_exists(PARENT_WWW_PATH . "js/" . $name . ".js")) {
            $parentfound = true;
            bpfw_register_js($name . "_js", PARENT_WWW_URI . "js/" . $name . ".js?v=10", true);
        }

        if (file_exists(BPFW_WWW_PATH . "js/" . $name . ".js")) {
            $bpfwfound = true;
            bpfw_register_js($name . "_js", BPFW_WWW_URI . "js/" . $name . ".js?v=10", true);
        }


        $name = strtolower($nameToCheck); // lower case
        if (!$appfound && file_exists(APP_WWW_PATH . "js/" . $name . ".js")) {
            $appfound = true;
            bpfw_register_js($name . "_js", APP_WWW_URI . "js/" . $name . ".js?v=10", true);
        }

        if (!$parentfound && PARENT_WWW_PATH != APP_WWW_PATH && file_exists(PARENT_WWW_PATH . "js/" . $name . ".js")) {
            $parentfound = true;
            bpfw_register_js($name . "_js", PARENT_WWW_URI . "js/" . $name . ".js?v=10", true);
        }

        if (!$bpfwfound && file_exists(BPFW_WWW_PATH . "js/" . $name . ".js")) {
            $bpfwfound = true;
            bpfw_register_js($name . "_js", BPFW_WWW_URI . "js/" . $name . ".js?v=10", true);
        }


        $name = lcfirst($nameToCheck); // starts with lowercase

        if (!$appfound && file_exists(APP_WWW_PATH . "js/" . $name . ".js")) {
            $appfound = true;
            bpfw_register_js($name . "_js", APP_WWW_URI . "js/" . $name . ".js?v=10", true);
        }

        if (!$parentfound && PARENT_WWW_PATH != APP_WWW_PATH && file_exists(PARENT_WWW_PATH . "js/" . $name . ".js")) {
            $parentfound = true;
            bpfw_register_js($name . "_js", PARENT_WWW_URI . "js/" . $name . ".js?v=10", true);
        }

        if (!$bpfwfound && file_exists(BPFW_WWW_PATH . "js/" . $name . ".js")) {
            $bpfwfound = true;
            bpfw_register_js($name . "_js", BPFW_WWW_URI . "js/" . $name . ".js?v=10", true);
        }
        
    }

    function tryIncludeCss($name): void
    {

        if (empty($name) || !is_string($name) || !ctype_alnum($name)) return;

        $nameToCheck = $name;

        $appfound = false;
        $parentFound = false;
        $bpfwFound = false;
        //var_dump($name);
        //echo APP_WWW_PATH." /js ".$name." .js";
        if (file_exists(APP_WWW_PATH . "css/" . $name . ".css")) {
            $appfound = true;
            bpfw_register_css($name . "_css", APP_WWW_URI . "css/" . $name . ".css", true);
        }

        if (PARENT_WWW_PATH != APP_WWW_PATH && file_exists(PARENT_WWW_PATH . "css/" . $name . ".css")) {
            $parentFound = true;
            bpfw_register_css($name . "_css", PARENT_WWW_URI . "css/" . $name . ".css", true);
        }

        if (file_exists(BPFW_WWW_PATH . "css/" . $name . ".css")) {
            $bpfwFound = true;
            bpfw_register_css($name . "_css", BPFW_WWW_URI . "css/" . $name . ".css", true);
        }


        $name = strtolower($nameToCheck); // lower case
        if (!$appfound && file_exists(APP_WWW_PATH . "css/" . $name . ".css")) {
            $appfound = true;
            bpfw_register_css($name . "_css", APP_WWW_URI . "css/" . $name . ".css", true);
        }

        if (!$parentFound && PARENT_WWW_PATH != APP_WWW_PATH && file_exists(PARENT_WWW_PATH . "css/" . $name . ".css")) {
            $parentFound = true;
            bpfw_register_css($name . "_css", PARENT_WWW_URI . "css/" . $name . ".css", true);
        }

        if (!$bpfwFound && file_exists(BPFW_WWW_PATH . "css/" . $name . ".css")) {
            $bpfwFound = true;
            bpfw_register_css($name . "_css", BPFW_WWW_URI . "css/" . $name . ".css?v=8", true);
        }


        $name = lcfirst($nameToCheck); // starts with lowercase

        if (!$appfound && file_exists(APP_WWW_PATH . "css/" . $name . ".css")) {
            $appfound = true;
            bpfw_register_css($name . "_css", APP_WWW_URI . "css/" . $name . ".css?v=8", true);
        }

        if (!$parentFound && PARENT_WWW_PATH != APP_WWW_PATH && file_exists(PARENT_WWW_PATH . "css/" . $name . ".css")) {
            $parentFound = true;
            bpfw_register_css($name . "_css", PARENT_WWW_URI . "css/" . $name . ".css", true);
        }

        if (!$bpfwFound && file_exists(BPFW_WWW_PATH . "css/" . $name . ".css")) {
            $bpfwFound = true;
            bpfw_register_css($name . "_css", BPFW_WWW_URI . "js/" . $name . ".css?v=8", true);
        }

    }

    /**
     * add js of models used inside components
     *
     * @param $dbmodel
     * @param string[] $ignore name of models to ignore (to prevent deadloop)
     * @return array
     * @throws Exception
     */
    function addChildJs($dbmodel, array $ignore): array
    {

        $modelComponents = array("modellist"); // list of components with models

        foreach ($dbmodel as $entry) {

            if (in_array($entry->display, $modelComponents) && !in_array($entry->display, $ignore)) {

                $modelnew = bpfw_createModelByName($entry->datamodel);

                $ignore[] = $entry->datamodel;

                $name = $entry->datamodel;
                $this->tryIncludeJs($name . "View");
                $this->tryIncludeJs($name . "Model");
                $this->tryIncludeCss($name . "View");
                $this->tryIncludeCss($name . "Model");
                $ignore = $this->addChildJs($modelnew->getDbModel(), $ignore);

            }

        }

        return $ignore;

    }

    /**
     * @throws Exception
     */
    function renderErrors(): void
    {


        ?>
        <!-- Modal error box -->
        <div style="z-index:9999" class="modal fade" id="errorMessageBox" tabindex="-1" role="dialog"
             aria-labelledby="ModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ModalLongTitle"></h5>
                        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                             <span aria-hidden="true">&times;</span>
                         </button> -->
                    </div>
                    <div class="modal-body" style="margin:auto; padding:20px;">
                        <i style="font-size:70px;margin:auto;" class="fas fa-spinner fa-spin"></i>
                    </div>
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                        <button type="button" class="btn btn-primary">Speichern</button> -->

                        <div class='buttonbar-afterform-wrap'>

                            <button data-dismiss="modal" type="button" name="close"
                                    class="btn btn-primary btn-default bpfwbutton button">

                                <div class="new-entry-button">
                                    <i class="topbuttonicon fas fa-check"></i>

                                    <div>
                                        Ok
                                    </div>

                                </div>

                            </button>

                        </div>


                    </div>
                </div>
            </div>
        </div>


        <div style="z-index:9999" class="modal fade" id="confirmMessageBox" tabindex="-1" role="dialog"
             aria-labelledby="ModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ModalLongTitle"></h5>
                        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button> -->
                    </div>
                    <div class="modal-body" style="margin:auto; padding:20px;">
                        <i style="font-size:70px;margin:auto;" class="fas fa-spinner fa-spin"></i>
                    </div>
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                                                <button type="button" class="btn btn-primary">Speichern</button> -->

                        <div class='buttonbar-afterform-wrap'>

                            <button id="confirmdialog_confirm" data-dismiss="modal" type="button" name="close"
                                    class="btn btn-primary btn-default bpfwbutton button">


                                <div class="new-entry-button">
                                    <i class="topbuttonicon fas fa-check"></i>

                                    <div>
                                        Ok
                                    </div>

                                </div>


                            </button>

                            <button id="confirmdialog_cancel" data-dismiss="modal" type="button" name="close"
                                    class="btn btn-primary btn-default bpfwbutton button cancelButton">

                                <div class="new-entry-button">
                                    <i class="topbuttonicon fas fa-times"></i>
                                    <div><?php echo __("Close"); ?></div>
                                </div>

                            </button>

                        </div>


                    </div>
                </div>
            </div>
        </div>


        <?php


        if (bpfw_error_hasErrors()) {

            ?>
            <div id="errorContainer">

                <?php foreach (bpfw_error_get() as $error) {
                    ?>

                    <p class="errorFrontend">
                        <?php echo $error->msg; ?>
                    </p>

                    <?php

                }
                ?>

            </div>

            <?php

        }
    }


    /**
     * Summary of renderView
     */
    function renderView(): void
    {
        ?>

        DefaultView.inc.php

        <?php
    }

    function registerJs(): void
    {

    }


}

