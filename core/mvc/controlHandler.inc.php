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



class ControlHandler
{

    private static string $currentClassName;
    var DefaultControl|null $currentControl = null;

    /**
     * @param BpfwModel $model
     * @return DefaultControl
     */
    public function createControl(BpfwModel $model): DefaultControl
    {

        // echo " createControl ";
        // echo ModelLoader::getCurrentPage();

        require_once(BPFW_MVC_PATH . "controls/defaultControl.inc.php");

        // TODO: allow lowercase?

        $default = $model->controltemplate;
        $classnames = array(ucwords((ModelLoader::getCurrentPage())) . "Control", strtolower((ModelLoader::getCurrentPage())) . "Control", strtolower((ModelLoader::getCurrentPage()) . "Control"), (ModelLoader::getCurrentPage()) . "Control", $default, "defaultControl");

        // die(BPFW_MVC_PATH . "controls". DIRECTORY_SEPARATOR . $default . "Control.inc.php");

        if (file_exists(APP_MVC_PATH . "controls". DIRECTORY_SEPARATOR . (ModelLoader::getCurrentPage()) . "Control.inc.php")) {
            require_once(APP_MVC_PATH . "controls". DIRECTORY_SEPARATOR . (ModelLoader::getCurrentPage()) . "Control.inc.php");
        } else if (file_exists(PARENT_MVC_PATH . "controls". DIRECTORY_SEPARATOR . (ModelLoader::getCurrentPage()) . "Control.inc.php")) {
            require_once(PARENT_MVC_PATH . "controls". DIRECTORY_SEPARATOR . (ModelLoader::getCurrentPage()) . "Control.inc.php");
        } else if (file_exists(BPFW_MVC_PATH . "controls". DIRECTORY_SEPARATOR . (ModelLoader::getCurrentPage()) . "Control.inc.php")) {
            require_once(BPFW_MVC_PATH . "controls". DIRECTORY_SEPARATOR . (ModelLoader::getCurrentPage()) . "Control.inc.php");
        } else if (file_exists(BPFW_MVC_PATH . "controls" . DIRECTORY_SEPARATOR . $default . ".inc.php")) {
            require_once(BPFW_MVC_PATH . "controls". DIRECTORY_SEPARATOR . $default . ".inc.php");
        } else if (file_exists(BPFW_MVC_PATH . "controls" . DIRECTORY_SEPARATOR . $default . "Control.inc.php")) {
            require_once(BPFW_MVC_PATH . "controls". DIRECTORY_SEPARATOR . $default . "Control.inc.php");
        }

        $control = null;

        foreach ($classnames as $val) {
            if (class_exists($val)) {

                // echo $val;

                static::$currentClassName = $val;
                $control = new static::$currentClassName(null);

                // $this->_data = $this->currentClassName::dbSelectAll();


                $this->currentControl = $control;

                // $bpfw_componentHandler->AddComponentsOfModel($model);

                // $this->executeControlHandler($this, $model);

                break;
            }
        }

        return $control;

    }

    /**
     * Summary of createControlAfterModelCreation
     *
     * @param DefaultControl $control
     * @param BpfwModel $model
     * @return BpfwModel
     * @throws Exception
     */
    public function EventAfterModelCreation(DefaultControl $control, BpfwModel $model): BpfwModel
    {
        global $database;

        foreach ($model->submitValues as $key => $value) {

            $field = $value->getDbField();

            if ($field->hiddenOnEdit || $field->doNotEdit) {

                if (isset($_POST['editAction'])) {
                    unset($model->submitValues[$key]);
                }

            }

            if ($field->display == "checkbox" && isset($model->submitValues[$key])) {

                if ($value->data == "on") {
                    $model->submitValues[$key]->data = 1;
                } else {
                    $model->submitValues[$key]->data = 0;
                }

            }

            if ($field->display == "combobox") {
                if ($value->data === '') {
                    $model->submitValues[$key]->data = "NULL";
                }
            }

        }


        if (!empty($control)) {
            $control->setModel($model);
        }
        /*switch($model->getSlug())
        {

        default:

        require_once(APP_MVC_PATH."controls/".$model->getSlug()."Control.inc.php");
        $this->executeControlHandler($this, $model);
        }*/

        $model->setControl($control);

        return $model;

    }

}