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

/**
 * @noinspection PhpUnused
 */

/**
 * Checkbox component for bpfw
 *
 * checkboxComponent description.
 *
 * @version 1.0
 * @author torst
 */
class ButtonComponent extends DefaultComponent
{

    /**
     * get html of displaying this element as a label (for example in a list)
     * @param mixed $value
     * @param string $fieldName
     * @param BpfwModelFormfield $fieldDbModel
     * @param int|string $rowKey
     * @param BpfwModel $model
     * @return string
     */
    public function GetDisplayLabelHtml(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, int|string $rowKey, BpfwModel $model): string
    {


        ob_start();


        $extrabutton_label = "(kein Label gesetzt)";
        if (!empty($fieldDbModel->label))
            $extrabutton_label = $fieldDbModel->label;

        $extrabutton_icon = "";
        if (!empty($fieldDbModel->buttonicon_form))
            $extrabutton_icon = $fieldDbModel->buttonicon_list;

        ?>

        <a data-rowkey='<?php echo $rowKey; ?>' data-buttonfor="<?php echo $fieldName; ?>"
           style='position:relative;left:-12px;padding-top:5px;'
           class="btn btn-secondary btn-default bpfwbutton button buttoncomponent buttoncomponent_list button_<?php echo $fieldName; ?>">
            <div class="new-entry-button">
                <i class="topbuttonicon <?php echo $extrabutton_icon; ?>"></i>
                <div>
                    <?php echo $extrabutton_label; ?>
                </div>
            </div>
        </a>

        <?php


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

        //  var_dump($fieldDbModel);


        $extrabutton_label = $fieldDbModel->label;

        $extrabutton_icon = $fieldDbModel->buttonicon_form;

        ?>

        <div style="display:inline-block">
            <a id="button-addedit-<?php echo $fieldName; ?>" data-rowkey='0' data-buttonfor="<?php echo $fieldName; ?>"
               style='position:relative;left:-12px;padding-top:5px;'
               class="btn btn-secondary btn-default bpfwbutton button buttoncomponent buttoncomponent_addoredit add button_<?php echo $fieldName; ?>">
                <div class="new-entry-button">
                    <i class="topbuttonicon <?php echo $extrabutton_icon; ?>"></i>
                    <div>
                        <?php echo $extrabutton_label; ?>
                    </div>
                </div>
            </a>
        </div>
        <?php


        return ob_get_clean();

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

        //  var_dump($fieldDbModel);


        $extrabutton_label = $fieldDbModel->label;

        $extrabutton_icon = $fieldDbModel->buttonicon_form;

        ?>
        <div style="display:inline-block">
            <a id="button-addedit-<?php echo $fieldName; ?>" data-rowkey='<?php echo $rowKey; ?>'
               data-buttonfor="<?php echo $fieldName; ?>" style='position:relative;left:-12px;padding-top:5px;'
               class="btn btn-secondary btn-default bpfwbutton button buttoncomponent buttoncomponent_addoredit edit button_<?php echo $fieldName; ?>">
                <div class="new-entry-button">
                    <i class="topbuttonicon <?php echo $extrabutton_icon; ?>"></i>
                    <div>
                        <?php echo $extrabutton_label; ?>
                    </div>
                </div>
            </a>
        </div>
        <?php


        return ob_get_clean();

    }


}