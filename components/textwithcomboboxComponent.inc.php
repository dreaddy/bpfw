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
class TextwithcomboboxComponent extends DefaultComponent
{

    function getCss(): string
    {
        return "

.textwithcombobox_wrapper{display:inline-block;}

.textwithcombobox_wrapper{
    max-width:388px;
}
.form_position_fullwidth .textwithcombobox_wrapper {
    max-width: initial;
}

.form_position_fullwidth .textwithcombobox_wrapper input[type='text']{
  margin-bottom:10px;
  float:left;
  margin-right:10px;
}

.component-textwithcombobox .bootstrap-select:not(.input-group-btn) {
    margin-left: 3px;
    padding-top: 7px;
    padding-bottom: 0px;
}

";
    }


    function getFooterJs(): string
    {
        ob_start();
        ?>

        <script>

            jQuery(document).ready(function () {

                jQuery(document).on("change", ".combobox_for_textbox_select_component", function (ee) {

                    //var selected = jQuery(ee).val();

                    const forField = jQuery(ee.target).data("selectfor");

                    jQuery('#' + forField).val(jQuery(ee.target).val());

                    // alert('input[name="' + forField + '"]');

                });

            });

        </script>

        <?php

        return ob_get_clean();

    }

    public function GetDisplayLabelHtml(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, int|string $rowKey, BpfwModel $model): string
    {

        if ($fieldDbModel->type->type == BpfwDbFieldType::TYPE_DECIMAL) {
            $value = bpfw_fromUsNumberFormat($value);
        }

        if ($this->conditionCheck($value, $fieldDbModel, $rowKey, $model)) {
            return $value;
        }

        return "";

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


        // combobox


        $xtraFormClass = isset($fieldDbModel->xtraFormClass) ? $fieldDbModel->display . " " . $fieldDbModel->xtraFormClass : $fieldDbModel->display;

        // echo $value;
        // var_dump($hvalue ["entries"]);


        // combobox

        $defavalueadded = false;

        if ($rowKey === 0) { // add
            if (empty($value)) {

                $value = $fieldDbModel->default;
                $defavalueadded = true;

            }
        }

        /*if(empty($value) && empty($rowkey)){
            if(isset($hvalue->default)){
                $value = $hvalue->default;
            }
        }*/

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


        if (!$disabled) {

            $type = "text";

            $stephtml = "";
            if ($fieldDbModel->type->type == BpfwDbFieldType::TYPE_INT) {
                $type = "number";
                if (!empty($fieldDbModel->step)) {
                    $step = (int)$fieldDbModel->step;
                    $stephtml = " step='$step' ";
                }

            }

            if ($fieldDbModel->type->type == BpfwDbFieldType::TYPE_DECIMAL) {
                $type = "number";
                $step = $fieldDbModel->step;
                $stephtml = " step='$step' ";

                //if(!isset($_POST[$hkey])){
                if ($defavalueadded) {
                    $value = bpfw_toUsNumberFormat($value);
                }

            }

            if ($drawIntervalSelection) {
                $xtraFormClass .= " hasintervalSelection";
            }

            $placeholder_title = "";
            if ($fieldDbModel->comboboxPlaceholderShow) {

                $placeholder_title = "title='" . $fieldDbModel->comboboxPlaceholderText . "'";


            }


            $prevalue = $value;
            /*<!-- name="selection_for_<?php echo $hkey; ?>" -->*/
            ?><div class="textwithcombobox_wrapper">
            <select data-live-search="true"
                <?php echo $this->getDataHtml($fieldDbModel->data); ?>
                    class="<?php echo $xtraFormClass; ?> bootstrap_admin_form_element admin_form_element selectpicker selection_for_<?php echo $fieldName; ?> combobox_for_textbox_select_component"
                    size="1"
                <?php echo $placeholder_title; ?>
                    data-selectfor="<?php echo $fieldName; ?>">
                <?php
                $values = $fieldDbModel->entries->getValueArray();
                if (!empty ($values)) {
                    foreach ($values as $skey => $svalue) {
                        echo '<option value="' . $skey . '" ';
                        if ($skey == $prevalue) {
                            //  echo "selected";
                        }
                        echo '>' . $svalue;
                        echo '</option>';
                    }
                }

                ?>
            </select>


            <?php

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

            echo '<input ' . $readonly . $this->getDataHtml($fieldDbModel->data) . ' ' . $autocomplatehtml . ' id="' . $fieldName . '_display" name="' . $fieldName . '_display" class = "' . $xtraFormClass . ' normal_admin_form_element admin_form_element" type="text" placeholder="' . $fieldDbModel->getPlaceholder() . '" ';
            echo ' value="' . bpfw_htmlentities($value) . '" ';
            echo " disabled ";
            echo '>';

            echo '<input ' . $readonly . $this->getDataHtml($fieldDbModel->data) . ' ' . $autocomplatehtml . ' id="' . $fieldName . '" name="' . $fieldName . '" type="hidden" placeholder="' . $fieldDbModel->getPlaceholder() . '" ';
            echo ' value="' . bpfw_htmlentities($value) . '" ';
            echo '>';
        }
        ?>
        </div> <?php
        return ob_get_clean();

    }


}