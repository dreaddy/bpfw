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
class ComboboxComponent extends DefaultComponent
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
    public function GetDisplayFormattedPlainValue(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, int|string $rowKey, BpfwModel $model): string
    {

        if ($this->conditionCheck($value, $fieldDbModel, $rowKey, $model)) {
            return $this->displayAsFormattedPlainValue($value, $fieldName, $fieldDbModel, $rowKey, $model);
        }
        return "";
    }

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


        $values = $fieldDbModel->entries->getValueArray();

        $displayval = $this->displayAsFormattedPlainValue($value, $fieldName, $fieldDbModel, $rowKey, $model);

        return "<div class='combobox_listview_label' data-value='$value' data-rowkey='$rowKey' >$displayval</div>";


    }

    /**
     * Summary of GetDisplayFormattedPlainValue
     * @param ?string $value
     * @param string $fieldname
     * @param BpfwModelFormfield $fieldDbModel
     * @param string $rowkey
     * @param BpfwModel $model
     * @return string
     */
    public function displayAsFormattedPlainValue(?string $value, string $fieldname, BpfwModelFormfield $fieldDbModel, string $rowkey, BpfwModel $model): string
    {

        if (empty($value) && $value !== 0 && $value !== "0") {
            $printval = "";
        }

        $printval = $fieldDbModel->entries->getValueByKey($value);

        if (empty($printval) && !empty($value)) {
            $printval = $value;
        }

        return $printval;

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
        // echo $value;
        // var_dump($hvalue ["entries"]);

        $prevalue = $value;

        ?>

        <select
                data-live-search="<?php echo ($fieldDbModel->combobox_livesearch) ? 'true' : 'false'; ?>"
            <?php echo $this->getDataHtml($fieldDbModel->data); ?>
                class="
    <?php echo $xtraFormClass; ?> bootstrap_admin_form_element admin_form_element selectpicker "
                name="<?php echo $fieldName; ?>"
                size="1" <?php if ($fieldDbModel->disabled) echo "disabled"; ?>>
            <?php
            $values = $fieldDbModel->entries->getValueArray();

            // COMBOBOX_PLACEHOLDER_TEXT

            if (!empty ($values)) {
                foreach ($values as $skey => $svalue) {
                    echo '<option value="' . $skey . '" ';
                    if ($skey == $prevalue) {
                        echo "selected";
                    }
                    echo '>' . $svalue;
                    echo '</option>';
                }
            }

            ?>
        </select>
        <?php

        if ($fieldDbModel->disabled) { // not comitted to $_POST otherwise
            echo "<input type='hidden' name='$fieldName' value='$prevalue'>";
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

        if (empty($value)) {
            $value = $fieldDbModel->default;
        }

        $prevalue = $value;


        ?>

    <select data-live-search="true"
        <?php echo $this->getDataHtml($fieldDbModel->data); ?>
            class="<?php echo $xtraFormClass; ?> bootstrap_admin_form_element admin_form_element selectpicker "
            name="<?php echo $fieldName; ?>"
            size="1" <?php if ($fieldDbModel->disabled) echo "disabled"; ?>>
        <?php

        $values = array();

        if (!empty($fieldDbModel->entries))
            $values = $fieldDbModel->entries->getValueArray();

        if (!empty ($values)) {
            foreach ($values as $skey => $svalue) {
                echo '<option value="' . $skey . '" ';
                if ($skey == $prevalue) {
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
