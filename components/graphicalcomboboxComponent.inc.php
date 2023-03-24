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
class GraphicalcomboboxComponent extends DefaultComponent
{


    function getFooterJs(): string
    {

        ob_start();

        if (bpfw_isAdmin()) { ?>

            <script>


                function jquerySaveCheckbox(element, userid, colkey, colvalue, rowkey, rowvalue) {

                    let url = "<?php echo BASE_URI;?>?p=<?php echo $_GET["p"];?>&ajaxCall=true&command=saveGraphicalcombobox";

                    $.post(url, {
                        userId: userid,
                        colkey: colkey,
                        colvalue: colvalue,
                        rowkey: rowkey,
                        rowvalue: rowvalue
                    })
                        .done(function () {
                            // check if all checked -> process Status -> only on events ...

                            if (typeof afterSaveGraphicalcomboboxHandler != 'undefined') {
                                if (jQuery.isFunction(afterSaveGraphicalcomboboxHandler)) {
                                    afterSaveGraphicalcomboboxHandler(element, userid, colkey, colvalue, rowkey, rowvalue);
                                }
                            }

                        });

                }

                jQuery(document).ready(function () {

                    jQuery(document).on('click', '.graphicalcomboxbox', function () {


                        let checkboxValue = jQuery(this).data("colvalue");

                        if (checkboxValue === 0) checkboxValue = 2;
                        else if (checkboxValue === 2) checkboxValue = 1;
                        else {
                            checkboxValue = 0;
                        }

                        jQuery(this).data("colvalue", checkboxValue);
                        jquerySaveCheckbox(jQuery(this), <?php echo bpfw_getUserId(); ?>, jQuery(this).data("colkey"), checkboxValue, jQuery(this).data("rowkey"), jQuery(this).data("rowvalue"));

                        jQuery(this).removeClass("checkOff");
                        jQuery(this).removeClass("checkOn");
                        jQuery(this).removeClass("checkProgress");

                        jQuery(this).removeClass("fa-check");
                        jQuery(this).removeClass("fa-times");
                        jQuery(this).removeClass("fa-hourglass-start");

                        //jQuery(this).removeClass("fa");
                        //  jQuery(this).removeClass("far");

                        if (checkboxValue === 0) {
                            jQuery(this).addClass("checkOff");
                            jQuery(this).addClass("fa-times");
                            // jQuery(this).addClass("fa");
                        } else if (checkboxValue === 1) {
                            jQuery(this).addClass("checkOn");
                            jQuery(this).addClass("fa-check");

                            // jQuery(this).addClass("fa");
                        } else {
                            jQuery(this).addClass("checkProgress");
                            jQuery(this).addClass("fa-hourglass-start");

                            // jQuery(this).addClass("fa");
                        }


                    });

                });

            </script>

            <?php

        }

        return ob_get_clean();

    }


    /**
     * Summary of displayAsFormattedPlainValue
     * @param mixed $value
     * @param string $fieldName
     * @param BpfwModelFormfield $fieldDbModel
     * @param int|string $rowKey
     * @param BpfwModel $model
     * @return string
     */
    public function GetDisplayFormattedPlainValue(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, int|string $rowKey, BpfwModel $model): mixed
    {

        return $value;

    }


    /**
     * Summary of displayAsLabel
     * @param mixed $value
     * @param string $fieldName
     * @param BpfwModelFormfield $fieldDbModel
     * @param int|string $rowKey
     * @param BpfwModel $model
     * @return string
     * @throws Exception
     */
    protected function displayAsLabel(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, int|string $rowKey, BpfwModel $model): string
    {

        ob_start();

        echo "<div>";

        $keyname = $model->getKeyName();
        $datastuff = $this->getDataHtml($fieldDbModel->data);
        echo "<div " . $datastuff . " style='display:inline-block'>";

        if ($value == 1) {
            ?>
            <i data-rowkey="<?php echo $keyname; ?>" <?php echo $datastuff; ?> data-rowvalue="<?php echo $rowKey; ?>"
               data-colkey="<?php echo $fieldName; ?>" data-colvalue="<?php echo $value; ?>"
               class="graphicalcomboxbox iconswitcher checkOn fa fa-check" aria-hidden="true"></i>
            <?php
        } else if ($value == 2) { ?>


            <i data-rowkey="<?php echo $keyname; ?>" <?php echo $datastuff; ?> data-rowvalue="<?php echo $rowKey; ?>"
               data-colkey="<?php echo $fieldName; ?>" data-colvalue="<?php echo $value; ?>"
               class="graphicalcomboxbox iconswitcher checkProgress fa fa-hourglass-start" aria-hidden="true"></i>

            <?php
        } else {
            ?>
            <i data-rowkey="<?php echo $keyname; ?>" <?php echo $datastuff; ?> data-rowvalue="<?php echo $rowKey; ?>"
               data-colkey="<?php echo $fieldName; ?>" data-colvalue="<?php echo $value; ?>"
               class="graphicalcomboxbox iconswitcher checkOff fa fa-times" aria-hidden="true"></i>
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

        $xtraFormClass = isset($fieldDbModel->xtraFormClass) ? $fieldDbModel->display . " " . $fieldDbModel->xtraFormClass : $fieldDbModel->display;

        ob_start();
        // echo $value;
        // var_dump($hvalue ["entries"]);

        $prevalue = $value;

        ?>

    <select data-live-search="false"
        <?php echo $this->getDataHtml($fieldDbModel->data); ?>
            class="<?php echo $xtraFormClass; ?> bootstrap_admin_form_element admin_form_element selectpicker "
            name="<?php echo $fieldName; ?>"
            size="1">
        <?php
        $values = array(0 => "Nein", 1 => "Ja", 2 => "in Arbeit");
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

        $prevalue = $value;
        ?>

    <select data-live-search="false"
        <?php echo $this->getDataHtml($fieldDbModel->data); ?>
            class="<?php echo $xtraFormClass; ?> bootstrap_admin_form_element admin_form_element selectpicker "
            name="<?php echo $fieldName; ?>"
            size="1">
        <?php
        $values = array(0 => "Nein", 1 => "Ja", 2 => "in Arbeit");
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