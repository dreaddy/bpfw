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
class CheckboxComponent extends DefaultComponent
{


    function getFooterJs(): string
    {

        ob_start();

        if (bpfw_isAdmin()) { ?>

            <script>

                function jquerySaveCheckbox(element, userid, colkey, colvalue, rowkey, rowvalue) {

                    let url = "<?php echo BASE_URI;?>?p=<?php echo $_GET["p"];?>&ajaxCall=true&command=saveCheckbox";

                    $.post(url, {
                        userId: userid,
                        colkey: colkey,
                        colvalue: colvalue,
                        rowkey: rowkey,
                        rowvalue: rowvalue
                    })
                        .done(function () {
                            // check if all checked -> process Status -> only on events ...

                            if (typeof afterSaveCheckboxHandler != 'undefined') {
                                if (jQuery.isFunction(afterSaveCheckboxHandler)) {
                                    afterSaveCheckboxHandler(element, userid, colkey, colvalue, rowkey, rowvalue);
                                }
                            }

                        });

                }

                jQuery(document).ready(function () {

                    jQuery(document).on('click', '.checkbox', function () {

                        let checkboxValue = jQuery(this).data("colvalue");

                        if (checkboxValue === 0) checkboxValue = 1;
                        else {
                            checkboxValue = 0;
                        }

                        jQuery(this).data("colvalue", checkboxValue);
                        jquerySaveCheckbox(jQuery(this), <?php echo bpfw_getUserId(); ?>, jQuery(this).data("colkey"), checkboxValue, jQuery(this).data("rowkey"), jQuery(this).data("rowvalue"));

                        jQuery(this).toggleClass("checkOff");
                        jQuery(this).toggleClass("checkOn");

                        jQuery(this).toggleClass("fa-check");
                        jQuery(this).toggleClass("fa-times");


                    });

                });

            </script>

            <?php

        }

        return ob_get_clean();

    }


    public function GetDisplayFormattedPlainValue(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, int|string $rowKey, BpfwModel $model): mixed
    {
        return $value;
    }

    /**
     * @throws Exception
     */
    protected function displayAsLabel(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, int|string $rowKey, BpfwModel $model): string
    {

        ob_start();

        echo "<div>";

        $keyName = $model->getKeyName();
        $datastuff = $this->getDataHtml($fieldDbModel->data);
        echo "<div " . $datastuff . " style='display:inline-block'>";

        if (empty($value)) {

            if ($value !== 0 && isset($fieldDbModel->default) && $fieldDbModel->default == 1) {
                $value = $fieldDbModel->default;
            } else {
                $value = 0;
            }

        }

        if ($value == 1) {
            ?>
            <i data-rowkey="<?php echo $keyName; ?>" <?php echo $datastuff; ?> data-rowvalue="<?php echo $rowKey; ?>"
               data-colkey="<?php echo $fieldName; ?>" data-colvalue="<?php echo $value; ?>"
               class="checkbox iconswitcher checkOn fa fa-check" aria-hidden="true"></i>
            <?php
        } else {
            ?>
            <i data-rowkey="<?php echo $keyName; ?>" <?php echo $datastuff; ?> data-rowvalue="<?php echo $rowKey; ?>"
               data-colkey="<?php echo $fieldName; ?>" data-colvalue="<?php echo $value; ?>"
               class="checkbox iconswitcher checkOff fa fa-times" aria-hidden="true"></i>
            <?php
        }

        echo "</div>";

        if ($fieldDbModel->showLabelInList) {
            echo "<div style='display:inline-block'>" . $fieldDbModel->label . "</div>";
        }
        echo "</div>";

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

        $xtraFormClass = $fieldDbModel->xtraFormClass ?? "";
        $cssClass = $fieldDbModel->display;

        $disabled = $fieldDbModel->disabled;

        // echo $value;

        //if($rowKey === 0)
        //    $disabled = ($fieldDbModel->disabled);

        echo '<input value="1" autocomplete="off"  id="' . $fieldName . '" name="' . $fieldName . '" class = "' . $cssClass . " " . $xtraFormClass . '  normal_admin_form_element admin_form_element" type="checkbox" placeholder="' . str_replace("<br>", "", $fieldDbModel->getPlaceholder()) . '" ';
        if ($disabled) {
            echo " disabled ";
        }
        if (!empty($value)) {
            echo " checked";
        }

        echo '>';

        // TODO: wird nicht submitted ...

        return ob_get_clean();

    }


}