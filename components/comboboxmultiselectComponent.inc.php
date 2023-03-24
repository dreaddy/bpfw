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
class ComboboxmultiselectComponent extends DefaultComponent
{

    function getRedrawJs(): string
    {
        ob_start();
        ?>

        jQuery(".selectpicker").selectpicker('refresh');

        <?php
        return ob_get_clean();


    }

    /**
     * Summary of displayAsLabel
     * @param mixed $value
     * @param string $fieldName
     * @param BpfwModelFormfield $fieldDbModel
     * @param int|string $rowKey
     * @param BpfwModel $model
     * @return string
     */
    protected function displayAsLabel(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, int|string $rowKey, BpfwModel $model): string
    {

        return $fieldDbModel->entries->getValueByKey($value);


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

        $xtraFormClass = isset($fieldDbModel->xtraFormClass) ? $fieldDbModel->display . " " . $fieldDbModel->xtraFormClass : $fieldDbModel->display;


        ob_start();


        $prevalue = null;
        if (isset ($_POST [$fieldName])) {
            $prevalue = $_POST [$fieldName];
        } else {
            if (!is_array($value)) {
                $json = stripslashes($value);
                $prevalue = json_decode($json);
            } else {
                $prevalue = $value;
            }

        }

        //   var_dump($prevalue);

        $values = $fieldDbModel->entries->getValueArray();
        foreach ($values as $skey => $svalue) {

            // echo " ".$skey."=>".$svalue." ";

            /*  if (!is_array ( $prevalue ) && ($skey == $prevalue || $svalue == $prevalue)) {
                  echo "selected";
              } else if (is_array ( $prevalue ) && (in_array ( $svalue, $prevalue ) || in_array ( $skey, $prevalue ) || in_array ( (int)$svalue, $prevalue ) || in_array ( (int)$skey, $prevalue ))    ) {
                  echo "selected";
              }*/

        }

        ?>

    <select data-live-search="true"
        <?php echo $this->getDataHtml($fieldDbModel->data); ?>
            class="<?php echo $xtraFormClass; ?> bootstrap_admin_form_element admin_form_element selectpicker "
            name="<?php echo $fieldName; ?>[]"
            size="1" <?php if ($fieldDbModel->disabled) echo "disabled"; ?>
            multiple>
        <?php


        if (!empty ($values)) {
            foreach ($values as $skey => $svalue) {
                echo '<option value="' . $skey . '" ';
                if (!is_array($prevalue) && ($skey == $prevalue || $svalue == $prevalue)) {
                    echo "selected";
                } else if (is_array($prevalue) && (in_array($svalue, $prevalue) || in_array($skey, $prevalue))) {
                    echo "selected";
                }
                echo '>';
                echo $svalue;
                echo '</option>';
            }
        }
        ?>
        </select><?php

        if ($fieldDbModel->disabled) { // not comitted to $_POST otherwise
            if (!is_array($prevalue)) {
                echo "<input type='hidden' name='" . $fieldName . "[]' value='" . $prevalue . "'>";
            } else {
                foreach ($prevalue as $val) {
                    echo "<input type='hidden' name='" . $fieldName . "[]' value='" . $val . "'>";
                }
            }
        }

        return ob_get_clean();
    }


    /**
     * Summary of displayAsAdd
     * @param mixed $value
     * @param string $fieldName
     * @param BpfwModelFormfield $fieldDbModel
     * @param BpfwModel $model
     * @return string
     */
    protected function displayAsAdd(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, BpfwModel $model): string
    {

        $xtraFormClass = isset($fieldDbModel->xtraFormClass) ? $fieldDbModel->display . " " . $fieldDbModel->xtraFormClass : $fieldDbModel->display;

        ob_start();

        $prevalue = null;
        if (isset ($_POST [$fieldName])) {
            $prevalue = $_POST [$fieldName];
        } else if (isset($fieldDbModel->default)) {
            $prevalue = $fieldDbModel->default;
        }

        ?>

    <select data-live-search="true"
        <?php echo $this->getDataHtml($fieldDbModel->data); ?>
            class="<?php echo $xtraFormClass; ?> bootstrap_admin_form_element admin_form_element selectpicker "
            name="<?php echo $fieldName; ?>[]"
            size="1" <?php if ($fieldDbModel->disabled) echo "disabled"; ?>
            multiple>
        <?php

        $values = $fieldDbModel->entries->getValueArray();

        if (!empty ($values)) {
            foreach ($values as $skey => $svalue) {
                echo '<option value="' . $skey . '" ';
                if (!is_array($prevalue) && ($skey == $prevalue || isset ($prevalue [$skey]))) {
                    echo "selected";
                } else if (is_array($prevalue) && (in_array($svalue, $prevalue) || in_array($skey, $prevalue))) {
                    echo "selected";
                }
                echo '>' . $svalue;
                echo '</option>';
            }
        }

        ?>
        </select><?php
        return ob_get_clean();
    }

}