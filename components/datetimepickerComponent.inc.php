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


require_once(bpfw_getIncludeFileForComponent("datepicker"));

/**
 * checkboxComponent short summary.
 *
 * checkboxComponent description.
 *
 * @version 1.0
 * @author torst
 */
class DatetimepickerComponent extends DatepickerComponent
{

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

                jQuery('.datetimepicker').each(function run() {

                    const settings = {
                        i18n: {
                            de: {
                                months: ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"],
                                dayOfWeek: ["So.", "Mo", "Di", "Mi", "Do", "Fr", "Sa."],
                            }
                        },
                        timepicker: true,
                        format: 'd.m.Y H:i',
                        step: 15,
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


                        let defaultdate = new Date();
                        defaultdate.setDate(defaultdate.getDate() + minDate);

                        settings["defaultDate"] = defaultdate; // new Date(settings["minDate"]);

                    }


                    if (jQuery(this).data("maxdate") !== undefined) {  // TODO

                        let maxDate = parseInt(jQuery(this).data("maxdate"));

                        if (maxDate > 31) maxDate = 31;
                        if (maxDate < -31) maxDate = -31;

                        let marDatePrefix = "";
                        if (maxDate <= 9 && maxDate >= -9) {
                            marDatePrefix = "0";
                        }

                        if (maxDate >= 0) {
                            settings["maxDate"] = "+1970/01/" + marDatePrefix + (maxDate + 1) + "";
                        }

                        if (maxDate < 0) {
                            settings["maxDate"] = "-1970/01/" + marDatePrefix + (maxDate * -1 + 1) + "";
                        }

                        // settings["defaultDate"] = settings["maxDate"]

                    }


                    // alert(JSON.stringify(settings));

                    jQuery(this).datetimepicker(settings)

                });

            });

        </script>

        <?php


        return ob_get_clean();

    }

    public function getMysqlValue($value)
    {
        if (empty($value)) return "1970-01-01 00:00:00";
        $value = bpfw_DateStringToTimestamp($value);
        return bpfw_TimestampTodatestring($value, "Y-m-d H:i:s");
    }

    public function preProcessBeforeSql(mixed $value): mixed
    {

        if (empty($value)) return "1970-01-01 00:00:00";

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

        //$dateformat = "d.m.Y H:i";
        $dateformat = bpfw_get_defaultDateFormat() . " H:i";
        if ($fieldDbModel->display == "datepicker") {
            $dateformat = bpfw_get_defaultDateFormat();
        } else if ($fieldDbModel->display == "timepicker") {
            $dateformat = "H:i";
        }

        $unformattedValue = $value;

        if (empty($unformattedValue) || $unformattedValue == "0000-00-00" || $unformattedValue == "0000-00-00 00:00:00" || $unformattedValue == "1970-01-01" || $unformattedValue == "1970-01-01 00:00:00") return "";
        else {

            return bpfw_mysqlDateStringToFormat($unformattedValue, $dateformat);

            //$phpdate = bpfw_datestringToTimestamp( $unformattedValue );
            //$formattedValue = date( $dateformat, $phpdate );
            //return $formattedValue;

        }


    }


}