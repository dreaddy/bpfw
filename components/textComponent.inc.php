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
class TextComponent extends DefaultComponent
{

    /**
     * get html of displaying this element as a label (for example in a list)
     * @param mixed $value
     * @param string $fieldName
     * @param BpfwModelFormfield $fieldDbModel
     * @param int|string $rowKey
     * @param BpfwModel $model
     * @return string
     * @throws Exception
     */
    public function GetDisplayLabelHtml(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, int|string $rowKey, BpfwModel $model): string
    {


        if ($fieldDbModel->type->type == BpfwDbFieldType::TYPE_DECIMAL) {
            $value = bpfw_fromUsNumberFormat($value);
        }


        return parent::GetDisplayLabelHtml($value, $fieldName, $fieldDbModel, $rowKey, $model);
    }

    /**
     * Summary of validateValue
     * @param array $errorsAlreadyFound
     * @param string $type
     * @param mixed $value
     * @param BpfwModelFormfield $headerValue
     * @param array $formValues
     * @param string $key
     * @param BpfwModel $model
     * @return array
     * @throws Exception
     */
    function validateValue(array $errorsAlreadyFound, string $type, mixed $value, BpfwModelFormfield $headerValue, array $formValues, string $key, BpfwModel $model): array
    {

        if ($headerValue->formfield_subtype == "email") {

            if (!empty($value)) {
                if ($value instanceof DbSubmitValue) {
                    if (!filter_var($value->data, FILTER_VALIDATE_EMAIL)) {
                        $errorsAlreadyFound[] = __("Invalid email");
                    }
                } else {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errorsAlreadyFound[] = __("Invalid email");
                    }
                }
            }

        }

        return parent::validateValue($errorsAlreadyFound, $type, $value, $headerValue, $formValues, $key, $model);

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

        $defaultValueAdded = false;

        /*if(empty($value) && empty($rowkey)){
            if(isset($hvalue->default)){
                $value = $hvalue->default;
            }
        }*/

        if ($rowKey === 0) { // add
            if (empty($value)) {

                $value = $fieldDbModel->default;
                $defaultValueAdded = true;
                /*  if(isset($hvalue->default)){
                      $value = $hvalue->default;
                  }*/
            }
        }

        $required = isset($fieldDbModel->required) && $fieldDbModel->required == true;
        $requiredTxt = ($required ? "required" : "");

        $xtraFormClass = isset($fieldDbModel->xtraFormClass) ? $fieldDbModel->display . " " . $fieldDbModel->xtraFormClass : $fieldDbModel->display;
        $disabled = ($fieldDbModel->disabled);

        $autocomplate = (isset ($fieldDbModel->autocomplete) && $fieldDbModel->autocomplete == true);

        $autocomplatehtml = "";


        $multipleComponents = false;
        $drawIntervalSelection = false;
        if ($fieldDbModel->display == "datepicker" && !empty($fieldDbModel->datetimeIntervalCombobox)) {
            $multipleComponents = true;
            $drawIntervalSelection = true;
        }


        if ($autocomplate) {
            $autocomplatehtml = "autocomplete='field-" . $fieldName . "' ";
        } else {
            $autocomplatehtml = 'autocomplete="off" '; // latest chrome is ignoring autocomplete off ... so also use js

            $xtraFormClass = trim($xtraFormClass) . " removeAutocomplete";
        }

        $step = 1;

        // no keyboard on mobile
        $readonly = "";
        if ($fieldDbModel->display == "datepicker" || $fieldDbModel->display == "timepicker" || $fieldDbModel->display == "datetimepicker") {
            $readonly = " readonly='true' ";
        }

        $type = "text";

        $stephtml = "";

        $pattern = "pattern=\"[0-9]+([,][0-9]+)?\"";

        if (!empty($fieldDbModel->pattern)) {
            $pattern = "pattern=" . $fieldDbModel->pattern;
            if ($fieldDbModel->pattern == "default") $pattern = "";
        }

        if ($fieldDbModel->type->type == BpfwDbFieldType::TYPE_INT) {

            $type = "number";

            if (!empty($fieldDbModel->step)) {
                $step = (int)$fieldDbModel->step;
                $stephtml .= " step='$step' $pattern ";
            }

            if (!empty($fieldDbModel->min) || $fieldDbModel->min == "0" || $fieldDbModel->min == 0) {
                $step = (int)$fieldDbModel->min;
                $stephtml .= " min='$step' ";
            }

            if (!empty($fieldDbModel->max) || $fieldDbModel->max == "0" || $fieldDbModel->max == 0) {
                $step = (int)$fieldDbModel->max;
                $stephtml .= " max='$step' ";
            }


        }

        if ($fieldDbModel->type->type == BpfwDbFieldType::TYPE_DECIMAL) {

            //

            $type = "number";
            $step = $fieldDbModel->step;
            $stephtml = " step='$step' $pattern ";

            //if(!isset($_POST[$hkey])){
            if ($defaultValueAdded) {
                $value = bpfw_toUsNumberFormat($value);
            }

        }

        if (!empty($fieldDbModel->textfield_type)) {
            $type = $fieldDbModel->textfield_type;
        }

        if ($drawIntervalSelection) {
            $xtraFormClass .= " hasintervalSelection";
        }

        if (!$disabled) {

            echo '<input ' . $readonly . $this->getDataHtml($fieldDbModel->data) . ' ' . $autocomplatehtml . ' id="' . $fieldName . '" name="' . $fieldName . '" class = "' . $xtraFormClass . ' normal_admin_form_element admin_form_element" type="' . $type . '" ' . $stephtml . ' placeholder="' . $fieldDbModel->getPlaceholder() . '" ';
            echo ' value="' . bpfw_htmlentities($value) . '" ';
            echo '>';

            if ($drawIntervalSelection) {

                // var_dump($hvalue->datetimeIntervalCombobox);

                ?>
                <select data-baseformfield="<?php echo $fieldDbModel->baseformfield; ?>" data-live-search="false"
                        data-picker_for="<?php echo $fieldName; ?>"
                        class="combobox admin_form_element intervalSelection selectpicker" name="customerId" size="1">
                    <?php

                    foreach ($fieldDbModel->datetimeIntervalCombobox as $key => $value) {
                        echo "<option value='$value'>$key</option>";

                    }

                    ?>
                </select>

                <?php

            }


        } else {

            echo '<input ' . $readonly . $this->getDataHtml($fieldDbModel->data) . ' ' . $autocomplatehtml . ' id="' . $fieldName . '_display" name="' . $fieldName . '_display" class = "' . $xtraFormClass . ' normal_admin_form_element admin_form_element" type="' . $type . '" placeholder="' . $fieldDbModel->getPlaceholder() . '" ';
            echo ' value="' . bpfw_htmlentities($value) . '" ';
            echo " disabled ";
            echo '>';

            echo '<input ' . $readonly . $this->getDataHtml($fieldDbModel->data) . ' ' . $autocomplatehtml . ' id="' . $fieldName . '" name="' . $fieldName . '" type="hidden" placeholder="' . $fieldDbModel->getPlaceholder() . '" ';
            echo ' value="' . bpfw_htmlentities($value) . '" ';
            echo '>';

        }

        return ob_get_clean();

    }


}