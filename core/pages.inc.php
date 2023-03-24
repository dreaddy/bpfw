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


if (file_exists(APP_BASE_PATH . "/functions.inc.php")) {
    require_once(APP_BASE_PATH . "/functions.inc.php");
}

if (file_exists(PARENT_BASE_PATH . "/functions.inc.php")) {
    require_once(PARENT_BASE_PATH . "/functions.inc.php");
}

require_once(BPFW_MVC_PATH . "bpfwDbFieldType.inc.php");

require_once(BPFW_MVC_PATH . "bpfwModelFormField.inc.php");
require_once(BPFW_MVC_PATH . "modelLoader.inc.php");
require_once(BPFW_MVC_PATH . "controlHandler.inc.php");
require_once(BPFW_MVC_PATH . "viewLoader.inc.php");

$modelLoader = new ModelLoader();
$controlhandler = new ControlHandler();
$viewloader = new ViewLoader();


$page = STARTING_PAGE;
if (!empty(getorpost('p'))) {
    $page = bpfw_getActivePage(); // $_GET['p'];
} else {

    if (bpfw_isAdmin()) {
        $page = bpfw_getActivePage();
    } else if (bpfw_hasConsultantPermissions()) {
        $page = bpfw_getActivePage();
    }

}

try {
    $model = $modelLoader->findAndCreateModel($page);
} catch (Exception $e) {
    echo "Exception " . $e->getMessage() . " - " . $e->getTraceAsString();
    die();
}
$control = $controlhandler->createControl($model);

if (!bpfw_hasPermission($model->minUserrankForShow)) {
    if (!bpfw_isLoggedIn()) {

        $page = "login";
        try {
            $model = $modelLoader->findAndCreateModel($page);
        } catch (Exception $e) {
            echo "Exception " . $e->getMessage() . " - " . $e->getTraceAsString();
            die();
        }

        $control = $controlhandler->createControl($model);
    }
}

if (!empty($model)) {
    $model->setControl($control);
    $control->setModel($model);
}

try {
    $view = $viewloader->createView($model);
} catch (Exception $e) {
    echo "Exception " . $e->getMessage() . " - " . $e->getTraceAsString();
    die();
}

$model->control->handleActions();
$model = $controlhandler->EventAfterModelCreation($control, $model);

try {
    $viewloader->render($model, $view);
} catch (Exception $e) {
    echo "viewloader Error: " . $e->getMessage();
    die();
}

