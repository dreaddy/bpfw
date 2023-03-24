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
class SpoilercontainerComponent extends DefaultComponent
{


    const LABELYES = "labelyes";
    const LABELNO = "labelno";

    /**
     * redraw after ajax added some components. No Script Tag!
     * @return string
     */
    function getCustomAttributes(): array
    {
        return array(SpoilercontainerComponent::LABELYES => "Yes", SpoilercontainerComponent::LABELNO => "No"); // array("customAttribute"=>123)
    }


    function GetDisplayEditHtml(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, BpfwModel $model, string|int $rowKey): string
    {


        return "";

    }

    /**
     * Summary of displayAsFormattedPlainValue
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


        $retval = "";

        $entries = $model->GetEntries();

        if (empty($entries[$rowKey])) {
            return "(WARNUNG: key '$rowKey' existiert nicht)";
        }

        $rowdata = $entries[$rowKey];


        $allok = true;

        $spoilercontent = "";

        $first = true;

        foreach ($model->getDbModel() as $k => $v) {

            // echo $fieldname;

            if ($v->parentComponent == $fieldName) {

                $component = bpfw_getComponentHandler()->getComponent($v->display);

                $v->data["parent"] = "$fieldName-$rowKey";

                $componentValue = $rowdata->$k; // TODO: need Data $v["data"]

                $val = $component->GetDisplayFormattedPlainValue($componentValue, $k, $v, $rowKey, $model);

                if ($val != "") {


                    if ($componentValue != 1) {
                        $allok = false;
                    }

                }

                $spoilercontent .= $val;
                $spoilercontent .= ",";

                $first = false;

                /// $retval.="<br>";


            }

        }

        return $spoilercontent;


    }

    function getFooterJs(): string
    {

        ob_start(); ?>

        <script>

            jQuery("document").ready(function () {

                function hideSpoiler(element) {
                    //jQuery(element).find(".spoilershower").show();
                    jQuery(".dataTable").find("#spoilercontent-" + jQuery(element).data("name")).hide();
                }

                function showSpoiler(element) {
                    //jQuery(element).find(".spoilershower").hide();
                    jQuery("#spoilercontent-" + jQuery(element).data("name")).show();
                }

                jQuery(".dataTable").on("touchstart mouseenter", ".spoilerwrapper",
                    function () {
                        showSpoiler(this);
                    }
                );

                jQuery(".dataTable").on("touchend mouseleave", ".spoilerwrapper",
                    function () {
                        hideSpoiler(this);
                    }
                );


                jQuery(".dataTable").on("click", ".spoilerwrapper",
                    function () {
                        if (jQuery("#spoilercontent-" + jQuery(this).data("name")).is(":visible")) {
                            // hideSpoiler(this);
                        } else {
                            // showSpoiler(this);
                        }
                    }
                );

            });

        </script>


        <?php
        return ob_get_clean();


    }

    protected function displayAsDuplicate(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, BpfwModel $model, string|int $rowKey): string
    {

        return "";

    }

    protected function displayAsAdd(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, BpfwModel $model): string
    {

        return "";
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


        $retval = "";

        $entries = $model->GetEntries();

        if (empty($entries[$rowKey])) {
            return "(WARNUNG: key '$rowKey' existiert nicht)";
        }

        $rowdata = $entries[$rowKey];


        $allok = true;
        $spoilercontent = "";
        foreach ($model->getDbModel() as $k => $v) {

            // echo $fieldname;

            if ($v->parentComponent == $fieldName) {

                $component = bpfw_getComponentHandler()->getComponent($v->display);

                $v->data["parent"] = "$fieldName-$rowKey";

                $componentValue = $rowdata->$k; // TODO: need Data $v["data"]

                $html = $component->GetDisplayLabelHtml($componentValue, $k, $v, $rowKey, $model);

                if ($html != "") {


                    if ($componentValue != 1) {
                        $allok = false;
                    }
                }

                $spoilercontent .= $html;
                /// $retval.="<br>";

            }

        }


        $retval .= "<div " . $this->getDataHtml($fieldDbModel->data) . " style='cursor:pointer; width:15px' data-name='$fieldName-$rowKey' id='spoilerwrapper-$fieldName-$rowKey' class='spoilerwrapper' >";
        $retval .= "<div " . $this->getDataHtml($fieldDbModel->data) . " style='width:15px' data-name='$fieldName-$rowKey' id='spoilershower-$fieldName-$rowKey' class='spoilershower' >" . ($allok ? $fieldDbModel->labelyes : $fieldDbModel->labelno) . "</div>";
        $retval .= "<div " . $this->getDataHtml($fieldDbModel->data) . " style='' id='spoilercontent-$fieldName-$rowKey' class='spoilercontent' data-rowkey='$rowKey' >";
        $retval .= $spoilercontent;
        $retval .= "</div>";
        $retval .= "</div>";

        return $retval;

    }

}
