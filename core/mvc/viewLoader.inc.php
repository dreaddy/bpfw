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




$ajax = !empty(getorpost("command"));

if (!$ajax) {
    // function spoil ?
}

class ViewLoader
{

    /**
     * Summary of render
     * @param BpfwModel $model
     * @param DefaultView $view
     * @return void
     * @throws Exception
     */
    public function render(BpfwModel $model, DefaultView $view): void
    {

        $usertype = bpfw_getCurrentUsertype();

        $canDisplay = bpfw_hasPermission($model->minUserrankForShow);
        $canEdit = bpfw_hasPermission($model->minUserrankForEdit);
        $canAdd = bpfw_hasPermission($model->minUserrankForAdd);
        $canDuplicate = bpfw_hasPermission($model->minUserrankForDuplicate);
        $canDelete = bpfw_hasPermission($model->minUserrankForDelete);
        $canPrint = bpfw_hasPermission($model->minUserrankForDelete);

        $err = $model->errors [$model->error] ?? array(
            'text' => $model->error,
            'css' => 'errorRed'
        );

        $addmode = isset ($_GET ['openAdd']);
        $editmode = isset ($_GET ['edit']) && isset ($model->data [$_GET ['edit']]);
        $duplicatemode = isset ($_GET ['duplicate']) && isset ($model->data [$_GET ['duplicate']]); // also sets editmode!

        $print = false;
        $hideNavigation = false;
        $ajax = bpfw_isAjax(); //getorpost("ajaxCall")=="true";

        if (!empty($_GET["showPrint"])) {
            $print = true;
            $hideNavigation = true;
        }

        if (!empty($_GET["hideNavigation"])) {
            $hideNavigation = true;
        }


        $filter = "";
        if (!empty(getorpost("filter"))) {
            $filter = getorpost("filter");
        }

        $currentpage = getorpost('p');
        if (empty($currentpage)) {
            $_GET['p'] = bpfw_getActivePage();
        }


        // var_dump($model->data);

        // var_dump($model->data);

        /*
         * echo "<pre>"; var_dump($model); echo "</pre>"
         */


        if (!$ajax && $model->showHeader) {

            if (file_exists(APP_VIEWS_PARTS_PATH . "header.inc.php")) {
                require_once(APP_VIEWS_PARTS_PATH . "header.inc.php");
            } else if (file_exists(PARENT_VIEWS_PARTS_PATH . "header.inc.php")) {
                require_once(PARENT_VIEWS_PARTS_PATH . "header.inc.php");
            } else {
                require_once(BPFW_VIEWS_PARTS_PATH . "header.inc.php");
            }

            if ($model->fullsizeHeaderbar) {

                if (!$hideNavigation && !$ajax && $model->showHeader2) {

                    if (file_exists(APP_VIEWS_PARTS_PATH . "headerbar.inc.php")) {
                        require_once(APP_VIEWS_PARTS_PATH . "headerbar.inc.php");
                    } else if (file_exists(PARENT_VIEWS_PARTS_PATH . "headerbar.inc.php")) {
                        require_once(PARENT_VIEWS_PARTS_PATH . "headerbar.inc.php");
                    } else {
                        require_once(BPFW_VIEWS_PARTS_PATH . "headerbar.inc.php");
                    }


                }

            }

        }

        if (!$hideNavigation && !$ajax && $model->showNavigation) {

            if (file_exists(APP_VIEWS_PARTS_PATH . "navigation.inc.php")) {
                require_once(APP_VIEWS_PARTS_PATH . "navigation.inc.php");
            } else if (file_exists(PARENT_VIEWS_PARTS_PATH . "navigation.inc.php")) {
                require_once(PARENT_VIEWS_PARTS_PATH . "navigation.inc.php");
            } else {
                require_once(BPFW_VIEWS_PARTS_PATH . "navigation.inc.php");
            }
        }

        if (!$canDisplay && !($addmode && $canAdd)) {
            return;
        }


        if (!$canEdit) {
            unset($_GET ['edit']);
        }

        if (!$canDelete) {
            unset($_GET ['delete']);
        }

        if (!$canAdd) {
            unset($_GET ['openAdd']);
        }

        if (!$canDuplicate) {
            unset($_GET ['duplicate']);
        }

        if (!$canPrint) {
            unset($_GET ['showPrint']);
        }

        if ($canDuplicate) {
            if (isset($_GET ['duplicate'])) {
                $_GET ['edit'] = $_GET ['duplicate'];
            }
        }

        if (!$ajax) { ?>

            <div id="mainContentWrapper" class="<?php if (!empty($_GET["showPrint"]) || !empty($_GET["hideNavigation"])) echo "noNavigation"; ?>" style="<?php if ($model->showNavigation) echo ""; ?>">

            <?php

            if (!$model->fullsizeHeaderbar) {

                if (!$hideNavigation && $model->showHeader2) {


                    if (file_exists(APP_VIEWS_PARTS_PATH . "headerbar.inc.php")) {
                        require_once(APP_VIEWS_PARTS_PATH . "headerbar.inc.php");
                    } else if (file_exists(PARENT_VIEWS_PARTS_PATH . "headerbar.inc.php")) {
                        require_once(PARENT_VIEWS_PARTS_PATH . "headerbar.inc.php");
                    } else {
                        require_once(BPFW_VIEWS_PARTS_PATH . "headerbar.inc.php");
                    }


                }

            }

            if ($model->showdata) {

                if (!empty($err ['text'])) {
                    echo "<h4 class = " . $err ['css'] . '>' . $err ['text'] . "</h4>";
                }

                echo "<h3 class='content-headline headline-stickleft'><div>";

                echo $model->getTitle();

                echo " ";

                if ($model->showdata) {
                    if ($editmode) {
                        echo "(Bearbeite Eintrag " . $_GET ['edit'] . ")";
                    } else {
                        // echo "(anlegen)";
                    }
                }

                echo "</div></h3>";

            }

            $this->printTopButtons($model, $editmode, $addmode, $print || $ajax, $filter);

        }

        if (!$ajax || null != getorpost("processViews")) {

            if (!empty($view) && is_object($view)) {
                $view->renderView();
            }

        }

        if (!$ajax) {
            ?>
            </div>
            <?php

        }


        if (!$ajax) {
            echo bpfw_get_footer_js();
        }

        if (!$ajax && $model->showFooter) {

            if (file_exists(APP_VIEWS_PARTS_PATH . "footer.inc.php")) {
                require_once(APP_VIEWS_PARTS_PATH . "footer.inc.php");
            } else if (file_exists(PARENT_VIEWS_PARTS_PATH . "footer.inc.php")) {
                require_once(PARENT_VIEWS_PARTS_PATH . "footer.inc.php");
            } else {
                require_once(BPFW_VIEWS_PARTS_PATH . "footer.inc.php");
            }


        }


        if (!$ajax) {

            ?>

            <div id="debugConsole" class="debugConsole" style="display:none">(Entwicklerkonsole - zum Schließen auf das
                Firmenlogo klicken)
            </div>

            <?php

        }

    }

    /**
     * @throws Exception
     * @noinspection PhpUnusedParameterInspection
     */
    public function printTopButtons(BpfwModel $model, bool $editMode, bool $addMode, bool $print, string $filter): void
    {

        bpfw_do_action(DefaultlistView::ACTION_DEFAULTLISTVIEW_BEFORE_RENDER_BUTTONS, $model->GetTableName());

        echo "<div class='buttonbar buttonbar-top'>";
        if ($model->showdata && $model->showTopButtons) {

            if (!$editMode) {
                if (!$print) {
                    $makePrint = bpfw_hasPermission($model->minUserrankForPrint); //bpfw_isAdmin();
                    $makeAdd = bpfw_hasPermission($model->minUserrankForAdd); //true; && !$addmode

                    if ($makeAdd) {
                        echo "<a data-hierachy='0' class = 'button addButton root_button'  href=\"?p=" . getorpost('p') . "&openAdd=true&filter=" . $filter . "\" >";
                        echo "<div data-hierachy='0' class='new-entry-button add-button root_button'><i class='topbuttonicon fa fa-plus'></i>";
                        echo "<div>";
                        echo __("New entry");
                        echo "</div>";
                        echo "</div>";
                        echo "</a>";
                    }

                    if ($makePrint) {
                        echo "<a data-hierachy='0' target='_blank' class = 'button printButton root_button'  href=\"?p=" . getorpost('p') . "&showPrint=true&filter=" . $filter . "\" >";
                        echo "<div data-hierachy='0' class='new-entry-button root_button'><i class='topbuttonicon fas fa-print'></i>";
                        echo "<div>";
                        echo __("Print");
                        echo "</div>";
                        echo "</div>";
                        echo "</a>";
                    }

                    // float left, also sind die eigentlich links
                    bpfw_do_action(DefaultlistView::ACTION_RENDER_CUSTOM_TOP_BUTTONS, $model->GetTableName());

                }
            }
        }
        echo "</div>";
    }

    /**
     * @throws Exception
     */
    function createView($model)
    {

        $debug = false;

        require_once(BPFW_MVC_PATH . "views/defaultView.inc.php");
        require_once(BPFW_MVC_PATH . "views/defaultlistView.inc.php");


        if ($debug) echo "check " . PARENT_MVC_PATH . "views/" . $model->getSlug() . "View.inc.php";

        if (file_exists(APP_MVC_PATH . "views/" . $model->getSlug() . "View.inc.php")) {
            require_once(APP_MVC_PATH . "views/" . $model->getSlug() . "View.inc.php");
        } else if (file_exists(PARENT_MVC_PATH . "views/" . $model->getSlug() . "View.inc.php")) {
            require_once(PARENT_MVC_PATH . "views/" . $model->getSlug() . "View.inc.php");
        } else if (file_exists(BPFW_MVC_PATH . "views/" . $model->getSlug() . "View.inc.php")) {
            require_once(BPFW_MVC_PATH . "views/" . $model->getSlug() . "View.inc.php");
        } else if (!empty($model->viewtemplate) && file_exists(BPFW_MVC_PATH . "views/" . $model->viewtemplate . ".inc.php")) {
            require_once(BPFW_MVC_PATH . "views/" . $model->viewtemplate . ".inc.php");
        } else if ($model->showdata) {
            require_once(BPFW_MVC_PATH . "views/defaultlistView.inc.php");
        }

        $current = $model->getSlug();
        $classnames = array(ucwords($current) . "View", strtolower($current) . "View", strtolower($current . "View"), $current . "View");

        $view = null;


        $currentClassName = null;

        foreach ($classnames as $val) {

            if (class_exists($val)) {

                $currentClassName = $val;
                $view = new $currentClassName($model);

                // $this->_data = $this->currentClassName::dbSelectAll();
                // $this->showdata = true;

                break;
            }
        }

        if (empty($view)) {

            if (!empty($model->viewtemplate)) {
                if (class_exists($model->viewtemplate)) {
                    $clasname = $model->viewtemplate;
                    $view = new $clasname($model);
                } else {
                    throw new Exception("want to use view " . $model->viewtemplate . " but class is not existing");
                }
            } else {

                if (!empty($model->getDbModel())) {

                    require_once(BPFW_MVC_PATH . "views/defaultlistView.inc.php");
                    $view = new DefaultlistView($model);
                } else {

                    require_once(BPFW_MVC_PATH . "views/defaultView.inc.php");
                    $view = new DefaultView($model);
                    echo "no dbmodel given. Can't use default db list model View, please provide a custom View.";
                    //   throw new Exception("no dbmodel given. Can't use default list model View, please provide a custom View.");

                }

            }
        }

        if (!empty($view) && method_exists($view, "initializeVariables")) {

            $view->initializeVariables();
        }

        return $view;

    }


}