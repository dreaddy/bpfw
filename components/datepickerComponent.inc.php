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
require_once(bpfw_getIncludeFileForComponent("text"));

class DatepickerComponent extends TextComponent
{


    function getRedrawJs(): string
    {

        ob_start();

        ?>

        jQuery('.datetimepicker_js').datetimepicker('destroy');

        <?php

        $footerjs = $this->getFooterJs();

        $footerjs = str_replace("<script>", "", $footerjs);
        $footerjs = str_replace("</script>", "", $footerjs);

        echo $footerjs;
        ?>

        var width = jQuery(window).width();

        if (width < 1400) {
        jQuery(".datetimepicker_js").attr("readonly", "true");
        } else {
        jQuery(".datetimepicker_js").removeAttr("readonly");
        }

        <?php

        return ob_get_clean();

    }

    function getFooterJs(): string
    {

        /*
         *
           minDate:'+1970/01/02'
           und maxDate
           */

        ob_start();
        ?>

        <script>

            jQuery(document).ready(function () {


                // datetimepicker init

                jQuery.datetimepicker.setLocale('de');

                jQuery('.datepicker').each(function run() {

                    const settings = {
                        i18n: {
                            de: {
                                months: ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"],
                                dayOfWeek: ["So.", "Mo", "Di", "Mi", "Do", "Fr", "Sa."],
                            }
                        },
                        timepicker: false,
                        format: 'd.m.Y',
                        dayOfWeekStart: 1,
                        inline: false,
                        validateOnBlur: true,

                    };

                    settings["defaultDate"] = new Date();

                    if (jQuery(this).data("mindate") !== undefined) {  // TODO


                        let minDate = parseInt(jQuery(this).data("mindate"));

                        let minDatePrefix = "";
                        if (minDate <= 9 && minDate >= -9) {
                            minDatePrefix = "0";
                        }

                        if (minDate > 31) minDate = 31;
                        if (minDate < -31) minDate = -31;


                        if (minDate >= 0) {
                            settings["minDate"] = "+1970/01/" + minDatePrefix + (minDate + 1) + "";
                        }

                        if (minDate < 0) {
                            settings["minDate"] = "-1970/01/" + minDatePrefix + (minDate * -1 + 1) + "";
                        }


                        const defaultdate = new Date();
                        defaultdate.setDate(defaultdate.getDate() + minDate);

                        settings["defaultDate"] = defaultdate; // new Date(settings["minDate"]);

                    }


                    if (jQuery(this).data("maxdate") !== undefined) {  // TODO

                        let maxDate = parseInt(jQuery(this).data("maxdate"));

                        if (maxDate > 31) maxDate = 31;
                        if (maxDate < -31) maxDate = -31;

                        let maxDatePrefix = "";
                        if (maxDate <= 9 && maxDate >= -9) {
                            maxDatePrefix = "0";
                        }

                        if (maxDate >= 0) {
                            settings["maxDate"] = "+1970/01/" + maxDatePrefix + (maxDate + 1) + "";
                        }

                        if (maxDate < 0) {
                            settings["maxDate"] = "-1970/01/" + maxDatePrefix + (maxDate * -1 + 1) + "";
                        }

                        // settings["defaultDate"] = settings["maxDate"]

                    }


                    // alert(JSON.stringify(settings));

                    // settings["defaultDate"] = "1.1.2020";

                    jQuery(this).datetimepicker(settings)

                });

                function addDays(date, days) {
                    const copy = new Date(Number(date))
                    copy.setDate(date.getDate() + days)
                    return copy
                }


// handle interval selector
                jQuery(".modal").on('hidden.bs.select', ".intervalSelection", function () {

                    if (this.value !== undefined) {

                        if (jQuery(this).data("baseformfield") !== "") {

                            const baseformfield = "#" + jQuery(this).data("baseformfield");

                            const dayoffset = parseInt(this.value, 10);

                            if (!isNaN(dayoffset)) {

                                const baseval = jQuery(baseformfield).val();

                                const dateparts = baseval.split(".");

                                const day = parseInt(dateparts[0], 10);
                                const month = parseInt(dateparts[1], 10);
                                const year = parseInt(dateparts[2], 10);

                                let newDate = new Date(year, month - 1, day); // month starts with 0=jan, 11=dec

                                newDate = addDays(newDate, dayoffset);

                                // newDate.setDate(newDate.getDate() + dayoffset);

                                //    var newDate = new Date( new Date(year, month-1, day).getTime() + 86400000 * dayoffset);

                                //   alert(newDate.getDate() + "." + (newDate.getMonth()+1) + "." + newDate.getYear());

                                jQuery("#" + jQuery(this).data("picker_for")).datetimepicker({
                                    value: newDate.getDate() + "." + (newDate.getMonth() + 1) + "." + newDate.getFullYear(),   // getMonth counts from 0 to 11
                                    format: 'd.m.Y'
                                });

                            }

                        }


                    }

                });

                //

                //

                // remove readonly for pc sizes

                function resizecheck() {
                    const width = jQuery(window).width();
                    if (width < 1400) {
                        jQuery(".datetimepicker_js").attr("readonly", "true");
                    } else {
                        jQuery(".datetimepicker_js").removeAttr("readonly");
                    }
                }

                jQuery(window).resize(function () {
                    resizecheck();
                });


            });


        </script>

        <?php

        return ob_get_clean();

    }

    /**
     * @throws Exception
     */
    function getSortValue(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, null|int|string $rowKey, BpfwModel $model): string|int|null
    {
        return bpfw_DateStringToTimestamp($value);
    }

    /**
     * @throws Exception
     */
    public function getMysqlValue($value)
    {
        if (empty($value)) return "1970-01-01 00:00:00";
        $value = bpfw_DateStringToTimestamp($value);
        return bpfw_TimestampTodatestring($value, "Y-m-d");
    }

    public function preProcessBeforeSql(mixed $value): mixed
    {

        if (empty($value)) return "1970-01-01 00:00:00";

        return $value;

    }

    /**
     * Summary of displayAsEdit
     * @param mixed $value
     * @param string $fieldName
     * @param BpfwModelFormfield $fieldDbModel
     * @param BpfwModel $model
     * @param string|int $rowKey
     * @return string
     * @throws Exception
     * @throws Exception
     */
    protected function displayAsEdit(mixed $value, string $fieldName, BpfwModelFormField $fieldDbModel, BpfwModel $model, string|int $rowKey): string
    {

        //      echo $value;

        $this->addCssClass($fieldDbModel, "datetimepicker_js");

        if (isset($fieldDbModel->max_date_days)) {
            if (empty($fieldDbModel->data)) $fieldDbModel->data = array();
            $fieldDbModel->data["maxDate"] = $fieldDbModel->max_date_days;
        }

        if (isset($fieldDbModel->min_date_days)) {
            if (empty($fieldDbModel->data)) $fieldDbModel->data = array();
            $fieldDbModel->data["minDate"] = $fieldDbModel->min_date_days;
        }

        $dateformat = bpfw_get_defaultDateFormat() . " H:i";
        if ($fieldDbModel->display == "datepicker") {
            $dateformat = bpfw_get_defaultDateFormat();
        } else if ($fieldDbModel->display == "timepicker") {
            $dateformat = "H:i";
        }

        if (empty($value) || $value == "0000-00-00" || $value == "0000-00-00 00:00:00" || $value == "1970-01-01" || $value == "1970-01-01 00:00:00" || $value == "00:00:00" || str_contains($value, "01.01.1970")) {
            $value = "";
        } else {

            $value = bpfw_mysqlDateStringToFormat($value, $dateformat);

            //var_dump($_POST);
            //echo $value;
            //$phpdate = bpfw_datestringToTimestamp( $value );
            /*echo $value;
            echo " ";
            echo $phpdate;*/
            //$formattedValue = date( $dateformat, $phpdate );
            //$value = $formattedValue;
            //echo $value;
        }

        return parent::displayAsEdit($value, $fieldName, $fieldDbModel, $model, $rowKey);


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

        //$dateformat = "d.m.y";
        /*
        $dateformat = "d.m.Y H:i";
        if($hvalue ["display"] == "datepicker"){
            $dateformat = "d.m.y";
        }else if($hvalue ["display"] == "timepicker"){
            $dateformat = "H:i";
        }*/

        $dateformat = bpfw_get_defaultDateFormat() . " H:i";
        if ($fieldDbModel->display == "datepicker") {
            $dateformat = bpfw_get_defaultDateFormat();
        } else if ($fieldDbModel->display == "timepicker") {
            $dateformat = "H:i";
        }


        $unformattedValue = $value;

        if (empty($value) || $unformattedValue == "0000-00-00" || $unformattedValue == "1970-01-01" || $unformattedValue == "0000-00-00 00:00:00" || $unformattedValue == "1970-01-01 00:00:00" || $unformattedValue == "00:00:00") return "";
        else {


            $displayval = bpfw_mysqlDateStringToFormat($unformattedValue, $dateformat);

            return "<div class='datepicker_listview_label' data-value='" . bpfw_datestringToTimestamp($unformattedValue) . "' data-rowkey='$rowKey' >$displayval</div>";


            /*
            $phpdate = bpfw_datestringToTimestamp( $unformattedValue );
            $formattedValue = date( $dateformat, $phpdate );
            return $formattedValue;*/

        }


    }

}
