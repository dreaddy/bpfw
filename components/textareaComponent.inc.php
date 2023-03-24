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
class TextareaComponent extends DefaultComponent
{

    function __construct($name, $componentHandler)
    {

        parent::__construct($name, $componentHandler);

        $this->showOnList = true;

    }

    protected function displayAsEdit(mixed $value, string $fieldName, BpfwModelFormField $fieldDbModel, BpfwModel $model, string|int $rowKey): string
    {

        $xtraFormClass = isset($fieldDbModel->xtraFormClass) ? $fieldDbModel->display . " " . $fieldDbModel->xtraFormClass : $fieldDbModel->display;
        $required = isset($fieldDbModel->required) && $fieldDbModel->required == true;
        $requiredTxt = ($required ? "required" : "");
        ob_start();

        echo "<span id='textarea_" . $fieldDbModel->name . "'>";

        $prevalue = $value;
        if (is_array($prevalue)) {
            $prevalue = json_encode($prevalue);
        }

        $disabled = ($fieldDbModel->disabled) ? "disabled" : "";


        $maxlength = "";
        if (!empty($fieldDbModel->maxlength)) {
            $maxlength = "maxlength='" . $fieldDbModel->maxlength . "'";
        }

        echo '<textarea  ' . $maxlength . " " . $disabled . ' ' . $this->getDataHtml($fieldDbModel->data) . ' ' . ' rows="10"  name="' . $fieldName . '" class = "' . $xtraFormClass . ' normal_admin_form_element admin_form_element defw400 ml_10px" placeholder="' . $fieldDbModel->getPlaceholder() . '" style="" >';
        if (!empty($prevalue)) {
            echo bpfw_htmlentities($prevalue);
        }
        //echo str_replace("\\r\\n", "\r\n", $prevalue); // TODO: Funktion suchen
        //echo $prevalue;
        echo "</textarea></span>";

        return ob_get_clean();

    }

    protected function displayAsAdd(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, BpfwModel $model): string
    {

        $xtraFormClass = isset($fieldDbModel->xtraFormClass) ? $fieldDbModel->display . " " . $fieldDbModel->xtraFormClass : $fieldDbModel->display;
        $required = isset($fieldDbModel->required) && $fieldDbModel->required == true;
        $requiredTxt = ($required ? "required" : "");

        ob_start();
        $prevalue = $value;
        if (is_array($prevalue)) {
            $prevalue = json_encode($prevalue);
        }
        echo "<span id='textarea_" . $fieldDbModel->name . "'>";


        $disabled = ($fieldDbModel->disabled) ? "disabled" : "";

        $maxlength = "";
        if (!empty($fieldDbModel->maxlength)) {
            $maxlength = "maxlength='" . $fieldDbModel->maxlength . "'";
        }

        echo '<textarea ' . $maxlength . ' ' . $disabled . ' ' . $this->getDataHtml($fieldDbModel->data) . ' ' . ' rows="10"  name="' . $fieldName . '" class = "' . $xtraFormClass . ' defw400 normal_admin_form_element admin_form_element ml_10px" placeholder="' . $fieldDbModel->getPlaceholder() . '" style="" >';

        echo bpfw_htmlentities($prevalue);

        echo "</textarea></span>";

        return ob_get_clean();

    }

}