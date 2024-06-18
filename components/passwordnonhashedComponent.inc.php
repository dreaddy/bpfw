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
class PasswordnonhashedComponent extends DefaultComponent
{

    function __construct($name, $componentHandler)
    {

        parent::__construct($name, $componentHandler);

        $this->showOnList = false;

    }

    /**
     * Summary of displayAsEdit
     * @param mixed $value
     * @param string $fieldName
     * @param BpfwModelFormfield $fieldDbModel
     * @param BpfwModel $model
     * @param string|int $rowKey
     * @return string
     */
    protected function displayAsEdit(mixed $value, string $fieldName, BpfwModelFormField $fieldDbModel, BpfwModel $model, string|int $rowKey): string
    {

        ob_start();

        $required = isset($fieldDbModel->required) && $fieldDbModel->required == true;
        $requiredTxt = ($required ? "required" : "");

        $xtraFormClass = isset($fieldDbModel->xtraFormClass) ? $fieldDbModel->display . " " . $fieldDbModel->xtraFormClass : $fieldDbModel->display;

        echo '<input ' . $this->getDataHtml($fieldDbModel->data) . " " . ' name="' . $fieldName . '" class = "' . $xtraFormClass . ' normal_admin_form_element admin_form_element" type="password" placeholder="' . $fieldDbModel->getPlaceholder() . '(keine &Auml;nderung)" >';

        return ob_get_clean();

    }

    /**
     * Summary of displayAsEdit
     * @param mixed $value
     * @param string $fieldName
     * @param BpfwModelFormfield $fieldDbModel
     * @param BpfwModel $model
     * @return string
     */
    protected function displayAsAdd(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, BpfwModel $model): string
    {

        ob_start();

        $required = isset($fieldDbModel->required) && $fieldDbModel->required == true;
        $requiredTxt = ($required ? "required" : "");

        $xtraFormClass = isset($fieldDbModel->xtraFormClass) ? $fieldDbModel->display . " " . $fieldDbModel->xtraFormClass : $fieldDbModel->display;

        echo '<input ' . $this->getDataHtml($fieldDbModel->data) . ' ' . ' name="' . $fieldName . '" class = "' . $xtraFormClass . ' normal_admin_form_element admin_form_element" type="password" placeholder="' . $fieldDbModel->getPlaceholder() . '" >';

        return ob_get_clean();

    }


}